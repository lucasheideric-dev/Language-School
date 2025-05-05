import React, { useState, useEffect } from "react";
import { getTeacher, saveTeacher } from "../../api/teachers";
import { toast } from "react-toastify";

const TeacherForm = ({ id, onClose }) => {
  const isEditMode = !!id;
  const [formData, setFormData] = useState({
    description: "",
    status: true,
    specialty: "",
  });

  const [error, setError] = useState("");
  const [loading, setLoading] = useState(false);
  const [specialties, setSpecialties] = useState([
    "Inglês",
    "Espanhol",
    "Francês",
    "Alemão",
    "Italiano",
  ]);
  const token = sessionStorage.getItem("token");

  useEffect(() => {
    const fetchTeacher = async () => {
      setLoading(true);
      try {
        const teacher = await getTeacher(id, token);
        setFormData({
          id: teacher.id || "",
          user_id: teacher.user_id || "",
          first_name: teacher.first_name || "",
          last_name: teacher.last_name || "",
          birth_date: teacher.birth_date || "",
          specialty: teacher.specialty || "",
          status: Boolean(teacher.status),
          cpf: teacher.cpf || "",
          created_at: teacher.created_at || "",
          updated_at: teacher.updated_at || "",
        });
      } catch (err) {
        setError(err.message || "Erro ao carregar dados do professor.");
      } finally {
        setLoading(false);
      }
    };

    if (isEditMode) fetchTeacher();
  }, [id, token]);

  const handleChange = (e) => {
    const { name, type, checked, value } = e.target;
    setFormData((prev) => ({
      ...prev,
      [name]: type === "checkbox" ? checked : value,
    }));
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    try {
      await saveTeacher(formData, token, id);
      isEditMode
        ? toast.success(`Professor atualizado com sucesso.`)
        : toast.success(`Professor cadastrado com sucesso.`);
      onClose();
    } catch (err) {
      setError(err.message || "Erro ao salvar dados.");
    }
  };

  return (
    <div>
      <h2 className="text-xl font-bold mb-4">
        {isEditMode ? "Editar Professor" : "Adicionar Professor"}
      </h2>

      {error && (
        <div className="bg-red-100 text-red-700 px-4 py-2 rounded mb-4">
          {error}
        </div>
      )}

      {loading ? (
        <div className="flex justify-center items-center mb-4">
          <div className="w-16 h-16 border-8 border-t-8 border-blue-500 border-solid rounded-full animate-spin border-t-transparent"></div>
        </div>
      ) : (
        <form onSubmit={handleSubmit}>
          <div className="mb-4">
            <label className="flex items-center space-x-2">
              <input
                type="checkbox"
                name="status"
                checked={formData.status}
                onChange={handleChange}
                className="form-checkbox h-5 w-5 text-blue-600"
              />
              <span className="text-gray-700">Ativo?</span>
            </label>
          </div>

          <div className="mb-4">
            <label className="block text-gray-700 mb-2" htmlFor="first_name">
              Nome *
            </label>
            <input
              type="text"
              name="first_name"
              value={formData.first_name || ""}
              onChange={handleChange}
              required
              className="border border-gray-300 rounded px-3 py-2 w-full"
            />
          </div>

          <div className="mb-4">
            <label className="block text-gray-700 mb-2" htmlFor="last_name">
              Sobrenome *
            </label>
            <input
              type="text"
              name="last_name"
              value={formData.last_name || ""}
              onChange={handleChange}
              required
              className="border border-gray-300 rounded px-3 py-2 w-full"
            />
          </div>

          <div className="mb-4">
            <label className="block text-gray-700 mb-2" htmlFor="cpf">
              CPF *
            </label>
            <input
              type="text"
              name="cpf"
              value={formData.cpf || ""}
              maxLength={14}
              onChange={handleChange}
              required
              className="border border-gray-300 rounded px-3 py-2 w-full"
            />
          </div>

          <div className="mb-4">
            <label className="block text-gray-700 mb-2" htmlFor="birth_date">
              Data de Nascimento
            </label>
            <input
              type="date"
              name="birth_date"
              value={formData.birth_date || ""}
              onChange={handleChange}
              className="border border-gray-300 rounded px-3 py-2 w-full"
            />
          </div>

          <div className="mb-4">
            <label className="block text-gray-700 mb-2" htmlFor="specialty">
              Especialidade
            </label>
            <div className="relative">
              <select
                name="specialty"
                value={formData.specialty || ""}
                onChange={handleChange}
                className="border border-gray-300 rounded-lg pl-3 pr-10 py-2 w-full focus:outline-none focus:ring-2 appearance-none"
              >
                <option value="" disabled>
                  Selecione a especialidade
                </option>
                {specialties.map((specialty, index) => (
                  <option key={index} value={specialty}>
                    {specialty}
                  </option>
                ))}
              </select>
              <div className="absolute top-0 right-0 mt-3 mr-3 pointer-events-none">
                <svg
                  className="w-4 h-4 text-gray-500"
                  xmlns="http://www.w3.org/2000/svg"
                  fill="none"
                  viewBox="0 0 24 24"
                  stroke="currentColor"
                >
                  <path
                    strokeLinecap="round"
                    strokeLinejoin="round"
                    strokeWidth="2"
                    d="M19 9l-7 7-7-7"
                  />
                </svg>
              </div>
            </div>
          </div>

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

export default TeacherForm;
