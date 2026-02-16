import axios from "axios";

const api = axios.create({
    baseURL: "/api",
    headers: {
        Accept: "application/json",
    },
});

api.interceptors.request.use(
    (config) => {
        // Si el payload es FormData, dejamos que el navegador ponga el boundary.
        if (config.data instanceof FormData) {
            delete config.headers["Content-Type"];
        } else {
            config.headers["Content-Type"] = "application/json";
        }

        const token = localStorage.getItem("token");
        if (token) {
            config.headers.Authorization = `Bearer ${token}`;
        }
        return config;
    },
    (error) => {
        return Promise.reject(error);
    },
);

api.interceptors.response.use(
    (response) => response,
    (error) => {
        if (error.response && error.response.status === 401) {
            localStorage.removeItem("token");
            window.location.href = "/auth";
        }
        return Promise.reject(error);
    },
);

export default api;
