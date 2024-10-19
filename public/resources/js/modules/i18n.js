window[appName].i18n = {
    translate: function(string) {
        return new Promise(async function(resolve, reject) {
            const serverInteraction = await fetch(`/translation/translate?translation=${string}`);
            resolve(await serverInteraction.json());
        });
    }
}