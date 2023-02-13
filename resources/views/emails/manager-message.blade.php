<table style="width: 100%; max-width: 320px; padding: 0; margin: auto; border: 0; border-spacing: 0;">
    <tr style="padding: 0; margin: 0; border: 0; border-spacing: 0;">
        <td style="padding: 0; margin: 0; border: 0; border-spacing: 0;">
            <p style="padding: 0; margin: 0; margin-block-start: 0; margin-block-end: 0;">
                Вам поступило сообщение из личного кабинета клиентов
            </p>
        </td>
    </tr>
    <tr style="padding: 0; margin: 0; border: 0; border-spacing: 0;">
        <td style="padding: 0; margin: 0; border: 0; border-spacing: 0; height: 8px;"></td>
    </tr>
    <tr style="padding: 0; margin: 0; border: 0; border-spacing: 0;">
        <td style="padding: 0; margin: 0; border: 0; border-spacing: 0;">
            <p style="padding: 0; margin: 0; margin-block-start: 0; margin-block-end: 0;">
                От: <a style="text-decoration: none;" href="mailto:{{ $user_email }}">{{ $user_email }}</a>
            </p>
        </td>
    </tr>
    <tr style="padding: 0; margin: 0; border: 0; border-spacing: 0;">
        <td style="padding: 0; margin: 0; border: 0; border-spacing: 0; height: 8px;"></td>
    </tr>
    <tr style="padding: 0; margin: 0; border: 0; border-spacing: 0;">
        <td style="padding: 0; margin: 0; border: 0; border-spacing: 0;">
            <p style="padding: 0; margin: 0; margin-block-start: 0; margin-block-end: 0;">
                Номер телефона: <a style="text-decoration: none;" href="tel:{{ $user_phone }}">{{ $user_phone }}</a>
            </p>
        </td>
    </tr>
    <tr style="padding: 0; margin: 0; border: 0; border-spacing: 0;">
        <td style="padding: 0; margin: 0; border: 0; border-spacing: 0; height: 8px;"></td>
    </tr>
    <tr style="padding: 0; margin: 0; border: 0; border-spacing: 0;">
        <td style="padding: 0; margin: 0; border: 0; border-spacing: 0;">
            <p style="padding: 0; margin: 0; margin-block-start: 0; margin-block-end: 0;">
                Сообщение: {{ $user_message }}
            </p>
        </td>
    </tr>
</table>
