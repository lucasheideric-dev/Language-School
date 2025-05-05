import React, { useState, useEffect } from "react";
import { getUser, saveUser } from "../../api/users";

const UserForm = ({ id, onClose }) => {
  const isEditMode = !!id;
  const [formData, setFormData] = useState({
    name: "",
    last_name: "",
    email: "",
    password: "",
  });
  const [error, setError] = useState("");
  const [loading, setLoading] = useState(false); // Estado para controlar o carregamento
  const token = sessionStorage.getItem("token");

  useEffect(() => {
    const fetchUser = async () => {
      setLoading(true); // Inicia o carregamento
      try {
        const user = await getUser(id, token);
        setFormData({
          name: user.name || "",
          last_name: user.last_name || "",
          email: user.email || "",
          password: "",
        });
      } catch (err) {
        setError(err.message || "Erro ao carregar dados do usuário.");
      } finally {
        setLoading(false); // Termina o carregamento
      }
    };

    if (isEditMode) fetchUser();
  }, [id]);

  const handleChange = (e) => {
    setFormData((prev) => ({
      ...prev,
      [e.target.name]: e.target.value,
    }));
  };

  const handleSubmit = async (e) => {
    e.preventDefault();

    try {
      await saveUser(formData, token, id);
      onClose();
    } catch (err) {
      setError(err.message || "Erro ao salvar dados.");
    }
  };

  return (
    <div>
      <h2 className="text-xl font-bold mb-4">
        {isEditMode ? "Editar Usuário" : "Adicionar Usuário"}
      </h2>

      {error && (
        <div className="bg-red-100 text-red-700 px-4 py-2 rounded mb-4">
          {error}
        </div>
      )}

      {loading ? (
        <div className="flex justify-center items-center">
          <div className="w-16 h-16 border-8 border-t-8 border-blue-500 border-solid rounded-full animate-spin border-t-transparent"></div>
        </div>
      ) : (
        <form onSubmit={handleSubmit} className="space-y-4">
          <input
            type="text"
            name="name"
            placeholder="Nome"
            value={formData.name}
            onChange={handleChange}
            className="w-full border px-4 py-2 rounded"
            required
          />
          <input
            type="text"
            name="last_name"
            placeholder="Sobrenome"
            value={formData.last_name}
            onChange={handleChange}
            className="w-full border px-4 py-2 rounded"
            required
          />
          <input
            type="email"
            name="email"
            placeholder="E-mail"
            value={formData.email}
            onChange={handleChange}
            className="w-full border px-4 py-2 rounded"
            required
          />
          {!isEditMode && (
            <input
              type="password"
              name="password"
              placeholder="Senha"
              value={formData.password}
              onChange={handleChange}
              className="w-full border px-4 py-2 rounded"
              required
            />
          )}
          <div className="flex justify-end gap-2">
            <button
              type="button"
              className="bg-gray-300 px-4 py-2 rounded"
              onClick={onClose}
            >
              Cancelar
            </button>
            <button
              type="submit"
              className="bg-[#00203F] text-white px-6 py-2 rounded hover:bg-[#002A51]"
            >
              {isEditMode ? "Atualizar" : "Salvar"}
            </button>
          </div>
        </form>
      )}
    </div>
  );
};

export default UserForm;
