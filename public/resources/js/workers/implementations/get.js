onmessage = async(e) => {
    const response = await fetch(e.data.url, {method: 'GET'});
    try {
        postMessage(await response.text() ?? '');
    } catch {
        postMessage('error');
    }
}