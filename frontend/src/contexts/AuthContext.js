import React, { createContext, useState, useContext, useEffect } from "react";
import { useNavigate } from "react-router-dom";
import { toast } from "react-toastify";

const AuthContext = createContext();

export const AuthProvider = ({ children }) => {
  const [token, setToken] = useState(
    localStorage.getItem("token") || sessionStorage.getItem("token")
  );
  const [user, setUser] = useState(
    JSON.parse(localStorage.getItem("user")) ||
      JSON.parse(sessionStorage.getItem("user"))
  );
  const navigate = useNavigate();

  useEffect(() => {
    if (token) {
      localStorage.setItem("token", token);
      sessionStorage.setItem("token", token);
    } else {
      localStorage.removeItem("token");
      sessionStorage.removeItem("token");
    }
    if (user) {
      localStorage.setItem("user", JSON.stringify(user));
      sessionStorage.setItem("user", JSON.stringify(user));
    } else {
      localStorage.removeItem("user");
      sessionStorage.removeItem("user");
    }
  }, [token, user]);

  const login = async ({ email, password }) => {
    const url = "http://127.0.0.1:8000/api/login";

    try {
      const response = await fetch(url, {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ email, password }),
      });

      const data = await response.json();

      if (response.ok && data.user && data.token) {
        setToken(data.token);
        setUser(data.user);

        toast.success(`Olá, ${data.user.first_name}, autenticação autorizada.`);
        navigate("/dashboard");
        return { success: true };
      } else {
        toast.error("Usuário ou senha inválidos. Tente novamente.");
        return { success: false, error: "Usuário ou senha inválidos." };
      }
    } catch (error) {
      toast.error("Erro ao conectar-se ao servidor.");
      return { success: false, error: "Erro ao conectar-se ao servidor." };
    }
  };

  const logout = () => {
    setToken(null);
    setUser(null);
  };

  return (
    <AuthContext.Provider value={{ token, user, login, logout }}>
      {children}
    </AuthContext.Provider>
  );
};

export const useAuth = () => {
  return useContext(AuthContext);
};
