import { useState } from "react";
import { useNavigate } from "react-router-dom";
import { toast } from "react-toastify";

export const useAuth = () => {
  const [labelButton, setLabelButton] = useState("Entrar");
  const [disabledButton, setDisabledButton] = useState(false);
  const [errorMessage, setErrorMessage] = useState("");
  const navigate = useNavigate();

  const login = async ({ email, password }) => {
    const url = "http://127.0.0.1:8000/api/login";

    try {
      setLabelButton("Carregando...");
      setDisabledButton(true);

      const response = await fetch(url, {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ email, password }),
      });

      const data = await response.json();

      if (response.ok && data.user && data.token) {
        sessionStorage.setItem("token", data.token);
        sessionStorage.setItem("user", JSON.stringify(data.user));

        localStorage.setItem("token", data.token);
        localStorage.setItem("user", JSON.stringify(data.user));

        toast.success("Olá, " + data.user.name + " autenticação autorizada.");
        navigate("/users");
      } else {
        const msg = "Usuário ou senha inválidos. Tente novamente.";
        setErrorMessage(msg);
        toast.error(msg);
      }
    } catch (error) {
      const msg = "Erro ao conectar-se ao servidor.";
      setErrorMessage(msg);
      toast.error(msg);
    } finally {
      setLabelButton("Entrar");
      setDisabledButton(false);
    }
  };

  return { login, errorMessage, labelButton, disabledButton };
};
