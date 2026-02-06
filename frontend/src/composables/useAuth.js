import { ref } from "vue";
import axios from "axios";

const usuario = ref({});
const loading = ref(false);
const error = ref(null);

export const useAuth = () => {
    const fetchUsuario = async () => {
        const token = localStorage.getItem("token");
        if (!token) return;

        loading.value = true;
        error.value = false;

        try {
            const response = await axios.get(
                "http://localhost:8080/api/datosuser",
                {
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Accept': "application/json",
                    },
                },
            );
            usuario.value = response.data;
        } catch (err) {
            error.value = err.response?.data?.message || "Error de conexión";
            console.error(err);
        } finally {
            loading.value = false;
        }
    };

    return {
        usuario, 
        loading,
        error,
        fetchUsuario
    }
};
