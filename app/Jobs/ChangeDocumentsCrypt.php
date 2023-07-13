<?php

namespace App\Jobs;

use App\Models\Document;
use App\Models\DocumentChangeEncryptionQueue;
use App\Models\Profile;
use App\Models\ProfileInternal;
use App\Packages\Crypto\File;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

class ChangeDocumentsCrypt implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(
        private readonly int $profileId,
        private readonly string $profileOldPassword,
    ) {
        $randomDocument = DocumentChangeEncryptionQueue::where('profile_id', '=', $profileId)->first();
        if ($randomDocument !== null) {
            throw new RuntimeException('На данный момент уже проходит другая смена пароля');
        }

        $internalIds = $this->getInternalIds();
        $documents = DB::table('document')
            ->select('document.*')
            ->leftJoin('invoice', 'document.order_id', '=', 'invoice.order_id')
            ->whereIn('invoice.user_id', $internalIds)
            ->get();

        foreach ($documents as $document){
            DocumentChangeEncryptionQueue::create([
                'document_id'           => $document->id,
                'old_profile_password'  => $profileOldPassword,
                'profile_id'            => $profileId,
            ]);
        }
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(): void
    {
        $profile = Profile::find($this->profileId);

        $queuedDocs = DocumentChangeEncryptionQueue::where('profile_id', '=', $this->profileId)->get();
        foreach($queuedDocs as $doc){
            $original_doc = Document::find($doc->document_id);

            // Документы могли обновиться с новым паролем
            if ($original_doc->created_at < $doc->created_at){
                $tmp = tmpfile();
                $tmpPath = stream_get_meta_data($tmp)['uri'];

                file_put_contents($tmpPath, Storage::disk('orders')->get($original_doc->order_id . '/'. $original_doc->filename));

                $decrypted_content = File::decrypt($tmpPath, $doc->old_profile_password);

                file_put_contents($tmpPath, $decrypted_content);
                File::encrypt($tmpPath, $profile->password);

                Storage::disk('orders')->put($original_doc->order_id . '/'. $original_doc->filename, file_get_contents($tmpPath));

                fclose($tmp);
            }

            $doc->delete();
        }
    }

    /**
     * @return array<int>
     * @throws RuntimeException
     */
    private function getInternalIds(): array
    {
        $internalProfiles = ProfileInternal::where('profile_id', $this->profileId)
            ->select('internal_id')
            ->get();

        if ($internalProfiles->isEmpty()) {
            throw new RuntimeException('Пользователь не найден');
        }

        $internalIds = [];
        foreach ($internalProfiles->all() as $profileInternal) {
            $internalIds[] = $profileInternal['internal_id'];
        }

        return $internalIds;
    }
}
