import React, { useState } from "react";
import { useAuth } from "../contexts/AuthContext";
import "react-toastify/dist/ReactToastify.css";

const Login = () => {
  const [email, setEmail] = useState("");
  const [password, setPassword] = useState("");
  const { login } = useAuth();
  const [errorMessage, setErrorMessage] = useState("");
  const [loading, setLoading] = useState(false);

  const handleSubmit = async (e) => {
    e.preventDefault();
    setLoading(true);
    const { success, error } = await login({ email, password });
    if (!success) {
      setErrorMessage(error);
    }
    setLoading(false);
  };

  return (
    <div className="h-full bg-[#002a51]">
      <div className="flex justify-center items-center h-screen p-5">
        <form
          className="bg-white p-10 rounded-xl shadow-xl w-full max-w-md text-center"
          onSubmit={handleSubmit}
        >
          <div className="mb-6">
            <img className="mx-auto w-20 h-20" src="/login.png" alt="Login" />
            <p className="text-lg font-medium mt-3 text-gray-800">
              Autenticação
            </p>
          </div>

          {errorMessage && (
            <div className="bg-red-100 text-red-600 text-sm p-3 mb-4 rounded-md">
              {errorMessage}
            </div>
          )}

          <div className="mb-5 text-left">
            <label htmlFor="email" className="block text-sm text-gray-700 mb-1">
              E-mail
            </label>
            <div className="relative">
              <input
                type="email"
                id="email"
                placeholder="Digite seu e-mail"
                value={email}
                onChange={(e) => setEmail(e.target.value)}
                required
                className="w-full pl-9 pr-3 py-3 border border-gray-300 rounded-md text-sm focus:outline-none focus:border-blue-900"
              />
              <span className="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 text-sm">
                @
              </span>
            </div>
          </div>

          <div className="mb-6 text-left">
            <label
              htmlFor="password"
              className="block text-sm text-gray-700 mb-1"
            >
              Senha
            </label>
            <div className="relative">
              <input
                type="password"
                id="password"
                placeholder="Digite sua senha"
                value={password}
                onChange={(e) => setPassword(e.target.value)}
                required
                className="w-full pl-9 pr-3 py-3 border border-gray-300 rounded-md text-sm focus:outline-none focus:border-blue-900"
              />
              <span className="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 text-sm">
                #
              </span>
            </div>
          </div>

          <button
            type="submit"
            disabled={loading}
            className={`w-full py-3 rounded-md text-white font-medium text-base transition-all ${
              loading
                ? "bg-[#002A51] opacity-70 cursor-not-allowed"
                : "bg-[#002A51] hover:translate-y-[-2px] hover:shadow-lg"
            }`}
          >
            {loading ? "Carregando..." : "Entrar"}
          </button>
        </form>
      </div>
    </div>
  );
};

export default Login;
