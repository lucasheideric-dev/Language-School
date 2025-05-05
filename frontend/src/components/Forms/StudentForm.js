import React, { useState, useEffect } from "react";
import { getStudent, saveStudent } from "../../api/students";
import { toast } from "react-toastify";

const StudentForm = ({ id, onClose }) => {
  const isEditMode = !!id;
  const [formData, setFormData] = useState({
    description: "",
    status: false,
    specialty: "",
  });

  const [availableCities, setAvailableCities] = useState([]);
  const [error, setError] = useState("");
  const [loading, setLoading] = useState(false);
  const [isHouseNumberSN, setIsHouseNumberSN] = useState(false); // Estado para o checkbox

  const token = sessionStorage.getItem("token");

  useEffect(() => {
    const fetchStudent = async () => {
      setLoading(true);
      try {
        const student = await getStudent(id, token);
        if (student) {
          setFormData({
            id: student.id || "",
            cpf: student.cpf || "",
            first_name: student.first_name || "",
            last_name: student.last_name || "",
            birth_date: student.birth_date || "",
            postal_code: student.postal_code || "",
            street: student.street || "",
            neighborhood: student.neighborhood || "",
            city: student.city || "",
            state: student.state || "",
            house_number: student.house_number || "",
            phone: student.phone || "",
            whatsapp: student.whatsapp || "",
            email: student.email || "",
          });

          if (student.state) {
            fetchCities(student.state);
          }
        } else {
          setError("Aluno não encontrado.");
        }
      } catch (err) {
        setError(err.message || "Erro ao carregar dados do aluno.");
      } finally {
        setLoading(false);
      }
    };

    if (isEditMode) fetchStudent();
  }, [id, token]);

  const handleChange = (e) => {
    const { name, type, checked, value } = e.target;

    if (name === "state") {
      setFormData((prev) => ({
        ...prev,
        state: value,
        city: "",
      }));
      fetchCities(value);
    } else {
      setFormData((prev) => ({
        ...prev,
        [name]: type === "checkbox" ? checked : value,
      }));
    }
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    try {
      await saveStudent(formData, token, id);

      isEditMode
        ? toast.success(`Aluno atualizado com sucesso.`)
        : toast.success(`Aluno cadastrado com sucesso.`);

      onClose();
    } catch (err) {
      setError(err.message || "Erro ao salvar dados.");
    }
  };

  const handlePostalCodeChange = async (e) => {
    const cep = e.target.value.replace(/\D/g, "");
    handleChange(e);

    if (cep.length === 8) {
      try {
        const response = await fetch(`https://viacep.com.br/ws/${cep}/json/`);
        const data = await response.json();
        if (!data.erro) {
          setFormData((prev) => ({
            ...prev,
            street: data.logradouro,
            neighborhood: data.bairro,
            city: data.localidade,
            state: data.uf,
          }));

          fetchCities(data.uf);
        }
      } catch (error) {
        console.error("Erro ao buscar endereço:", error);
      }
    }
  };

  const fetchCities = async (uf) => {
    try {
      const response = await fetch(
        `https://servicodados.ibge.gov.br/api/v1/localidades/estados/${uf}/municipios`
      );
      const data = await response.json();

      const cityNames = data.map((city) => city.nome).sort();
      setAvailableCities(cityNames);
    } catch (error) {
      console.error("Erro ao buscar cidades do IBGE:", error);
      setAvailableCities([]);
    }
  };

  const handleHouseNumberChange = (e) => {
    let value = e.target.value;
    if (isHouseNumberSN) {
      value = "SN";
    } else {
      // Permite números e 'SN'
      value = value.replace(/[^0-9]/g, "");
    }
    setFormData((prev) => ({
      ...prev,
      house_number: value,
    }));
  };

  const handleSNCheckboxChange = (e) => {
    const checked = e.target.checked;
    setIsHouseNumberSN(checked);

    if (checked) {
      setFormData((prev) => ({
        ...prev,
        house_number: "SN",
      }));
    } else {
      setFormData((prev) => ({
        ...prev,
        house_number: "",
      }));
    }
  };

  return (
    <div>
      <h2 className="text-xl font-bold mb-4">
        {isEditMode ? "Editar Aluno" : "Adicionar Aluno"}
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
          <div className="flex gap-4 mb-4">
            <div className="flex-1">
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

            <div className="flex-1">
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
          </div>
          <div className="flex gap-4 mb-4">
            {!isEditMode && (
              <div className="flex-1">
                <label className="block text-gray-700 mb-2" htmlFor="cpf">
                  CPF *
                </label>
                <input
                  type="text"
                  name="cpf"
                  maxLength={14}
                  value={formData.cpf || ""}
                  onChange={handleChange}
                  required
                  className="border border-gray-300 rounded px-3 py-2 w-full"
                />
              </div>
            )}
            <div className="flex-1">
              <label className="block text-gray-700 mb-2" htmlFor="birth_date">
                Data de Nascimento
              </label>
              <input
                type="date"
                name="birth_date"
                value={formData.birth_date || ""}
                onChange={handleChange}
                required
                className="border border-gray-300 rounded px-3 py-2 w-full"
              />
            </div>
          </div>

          <div className="flex gap-4 mb-4">
            <div className="flex-1">
              <label className="block text-gray-700 mb-2" htmlFor="phone">
                Telefone
              </label>
              <input
                type="tel"
                name="phone"
                maxLength={14}
                value={formData.phone || ""}
                onChange={handleChange}
                className="border border-gray-300 rounded px-3 py-2 w-full"
              />
            </div>
            <div className="flex-1">
              <label className="block text-gray-700 mb-2" htmlFor="whatsapp">
                WhatsApp *
              </label>
              <input
                type="tel"
                name="whatsapp"
                maxLength={15}
                value={formData.whatsapp || ""}
                onChange={handleChange}
                required
                className="border border-gray-300 rounded px-3 py-2 w-full"
              />
            </div>
          </div>

          <div className="mb-4">
            <label className="block text-gray-700 mb-2" htmlFor="email">
              E-mail
            </label>
            <input
              type="email"
              name="email"
              value={formData.email || ""}
              onChange={handleChange}
              className="border border-gray-300 rounded px-3 py-2 w-full"
            />
          </div>

          <div className="mb-4">
            <label className="block text-gray-700 mb-2" htmlFor="postal_code">
              CEP *
            </label>
            <input
              type="text"
              name="postal_code"
              value={formData.postal_code || ""}
              onChange={handlePostalCodeChange}
              required
              className="border border-gray-300 rounded px-3 py-2 w-full"
              maxLength={8}
            />
          </div>

          <div className="flex gap-4 mb-4">
            <div className="flex-1">
              <label className="block text-gray-700 mb-2" htmlFor="street">
                Rua *
              </label>
              <input
                type="text"
                name="street"
                value={formData.street || ""}
                onChange={handleChange}
                required
                className="border border-gray-300 rounded px-3 py-2 w-full"
                readOnly
              />
            </div>
            <div className="w-1/3">
              <label
                className="block text-gray-700 mb-2"
                htmlFor="house_number"
              >
                Número *
              </label>
              <input
                type="text"
                name="house_number"
                value={formData.house_number || ""}
                onChange={handleHouseNumberChange}
                className="border border-gray-300 rounded px-3 py-2 w-full"
                placeholder="Ex: 123 ou SN"
                required
                disabled={isHouseNumberSN}
              />
              <div>
                <label className="inline-flex items-center mt-2">
                  <input
                    type="checkbox"
                    onChange={handleSNCheckboxChange}
                    checked={isHouseNumberSN}
                    className="form-checkbox"
                  />
                  <span className="ml-2">Número SN</span>
                </label>
              </div>
            </div>
          </div>

          <div className="mb-4">
            <label className="block text-gray-700 mb-2" htmlFor="neighborhood">
              Bairro *
            </label>
            <input
              type="text"
              name="neighborhood"
              value={formData.neighborhood || ""}
              onChange={handleChange}
              required
              className="border border-gray-300 rounded px-3 py-2 w-full"
              readOnly
            />
          </div>

          <div className="flex gap-4 mb-4">
            <div className="w-1/4">
              <label className="block text-gray-700 mb-2" htmlFor="state">
                Estado *
              </label>
              <input
                type="text"
                name="state"
                value={formData.state || ""}
                onChange={handleChange}
                required
                className="border border-gray-300 rounded px-3 py-2 w-full"
                readOnly
              />
            </div>
            <div className="flex-1">
              <label className="block text-gray-700 mb-2" htmlFor="city">
                Cidade *
              </label>
              {availableCities.length > 0 ? (
                <select
                  name="city"
                  value={formData.city || ""}
                  onChange={handleChange}
                  className="border border-gray-300 rounded px-3 py-2 w-full"
                  required
                >
                  <option value="">Selecione uma cidade</option>
                  {availableCities.map((city) => (
                    <option key={city} value={city}>
                      {city}
                    </option>
                  ))}
                </select>
              ) : (
                <input
                  type="text"
                  name="city"
                  value={formData.city || ""}
                  onChange={handleChange}
                  className="border border-gray-300 rounded px-3 py-2 w-full"
                  readOnly
                />
              )}
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
              className="bg-[#00203F] text-white px-4 py-2 rounded"
            >
              Salvar
            </button>
          </div>
        </form>
      )}
    </div>
  );
};

export default StudentForm;
