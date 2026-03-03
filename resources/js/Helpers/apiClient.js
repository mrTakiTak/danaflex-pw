import axios from "axios";

// Создаем экземпляр клиента Axios с базовыми настройками
const apiClient = axios.create({});

// Функция для обработки на клиенте
const handleApiClientError = (error) => {
    let urlEncoded;
    if (error.response) {
        switch (error.response.status) {
            case 419:
            case 401:
                location.reload();
                return;
            case 403:
                urlEncoded = encodeURIComponent(window.location.href);
                window.location.href = `/403?href=${urlEncoded}`;
                return;

            default:
                console.error(`API Error: ${error.response.status} - ${error.response.data.message}`);
        }
    } else {
        console.error(`Network Error: ${error.message}`);
    }

    let errorText = "";
    if (typeof error.response.data === "string") {
        errorText = error.response.data.slice(0, 10000);
    } else {
        errorText = JSON.stringify(error.response.data).slice(0, 10000);;
    }
    alert(`ОШИБКА! ${errorText}`);
};

// Интерцептор для запросов
apiClient.interceptors.request.use(
    (config) => {
        return config;
    },
    (error) => {
        return Promise.reject(error);
    }
);

// Интерцептор для ответов
apiClient.interceptors.response.use(
    (response) => {
        if (isHtml(response.data)) {
            showContentDialog(response.data);
        }
        return response;
    }, // Возвращаем ответ, если все прошло успешно
    (error) => {
        if (isHtml(error?.response?.data)) {
            showContentDialog(error.response.data, true);
            return Promise.reject(error);
        }
        handleApiClientError(error);
        return Promise.reject(error);
    } // Обрабатываем ошибку с помощью внешней функции
);

const isHtml = (content) => {
    const isHtmlDirty = /<(!DOCTYPE html|html|body|head|div|span|p|a|img|table)[\s>]/i.test(content);
    if (!isHtmlDirty) return false;
    try {
        const parser = new DOMParser();
        const doc = parser.parseFromString(content, "text/html");
        if (doc.body && doc.body.children.length > 0) {
            return true;
        }
    } catch (e) {
        return false;
    }
    return false;
};

const showContentDialog = (contentHtmlText, isError = false) => {
    const app = document.getElementById("app");
    // Создаём контейнер диалога

    const existing = document.getElementById("dialog");
    if (existing) existing.remove();

    const dialog = document.createElement("div");
    dialog.id = "dialog";
    Object.assign(dialog.style, {
        position: "fixed",
        top: "50px",
        left: "50px",
        right: "50px",
        bottom: "50px",
        background: "#fff",
        boxShadow: "0 0 20px rgba(0, 0, 0, 0.3)",
        borderRadius: "8px",
        zIndex: "1000",
        display: "flex",
        flexDirection: "column",
        padding: "20px"
    });

    if (isError) {
        const header = document.createElement("div");
        Object.assign(header.style, {
            marginBottom: "10px",
            color: "red"
        });
        header.textContent = "Ошибка выполнения запроса к серверу: ";
        dialog.appendChild(header);
    }

    // Блок контента с прокруткой
    const content = document.createElement("div");
    Object.assign(content.style, {
        flex: "1",
        overflowY: "auto",
        marginBottom: "20px"
    });

    content.innerHTML = contentHtmlText;

    // Кнопка закрытия
    const closeBtn = document.createElement("button");
    closeBtn.textContent = "Закрыть";
    Object.assign(closeBtn.style, {
        alignSelf: "flex-end",
        backgroundColor: "#007BFF",
        color: "#fff",
        border: "none",
        padding: "10px 20px",
        borderRadius: "6px",
        cursor: "pointer",
        fontSize: "14px"
    });
    closeBtn.onmouseenter = () => (closeBtn.style.backgroundColor = "#0056b3");
    closeBtn.onmouseleave = () => (closeBtn.style.backgroundColor = "#007BFF");
    closeBtn.onclick = () => dialog.remove();

    dialog.appendChild(content);
    dialog.appendChild(closeBtn);
    app.appendChild(dialog);
};

// Экспортируем настроенный экземпляр клиента Axios
export default apiClient;
