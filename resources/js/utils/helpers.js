export function isServer() {
    return typeof window === 'undefined';
}

export function setGlobalVariables(props) {
    global.base_url = props.base_url;
    global.csrf_token = props.csrf_token;
}

export function getBaseUrl() {
    if (!isServer()) {
        return document.querySelector('meta[name="base-url"]').getAttribute("content");
    }
    return global.base_url;
}

export function getCsrfToken() {
    if (!isServer()) {
        return document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    }
    return global.csrf_token;
}

export function generateVariantTitle(combination) {
    if (Array.isArray(combination)) {
        return combination.join('/');
    }
    return combination;
}
