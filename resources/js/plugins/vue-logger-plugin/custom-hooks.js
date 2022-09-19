const sendErrorsToServer = {
    run(logEvent) {
        console.log(logEvent);
        const serverLoggableType = ['error', 'fatal'];
    }
};

export {sendErrorsToServer};
