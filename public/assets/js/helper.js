function updateURLParameter(url, param, paramVal) {
    let baseUrl = new URL(url);
    let params = new URLSearchParams(baseUrl.search);
    if (params.has(param)) {
        params.set(param, paramVal);
    } else {
        params.append(param, paramVal);
    }
    baseUrl.search = params;
    return baseUrl.href;
}

function deleteURLParameter(url, param) {
    let baseUrl = new URL(url);
    let params = new URLSearchParams(baseUrl.search);
    if (params.has(param)) {
        params.delete(param);
    }
    baseUrl.search = params;
    return baseUrl.href;
}
