const storageBaseUrl = (() => {
    const explicit = import.meta.env.VITE_STORAGE_URL;
    if (explicit) return explicit.replace(/\/+$/, "");

    const api = import.meta.env.VITE_API_URL || "";
    if (!api) return "";

    return api.replace(/\/api\/?$/, "");
})();

export const storageUrl = (path) => {
    if (!path) return "";
    return storageBaseUrl ? `${storageBaseUrl}/storage/${path}` : `/storage/${path}`;
};

export const storageDefaultProductUrl = () => {
    return storageBaseUrl
        ? `${storageBaseUrl}/storage/productos/default.png`
        : "/storage/productos/default.png";
};
