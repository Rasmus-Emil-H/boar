window[appName].i18n = {
    translate: function(string) {
        return new Promise(async function(resolve, reject) {
            try {
                const serverInteraction = await $.get(`/translation/translate?translation=${string}`);
                resolve(await serverInteraction.json());
            } catch {
                reject(string);
            }
        });
    }
}