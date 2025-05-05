const API_URL = "http://127.0.0.1:8000/api/guardianForms";

export const fetchGuardianForms = async (token) => {
  const response = await fetch(API_URL, {
    headers: {
      Authorization: `Bearer ${token}`,
      "Content-Type": "application/json",
    },
  });

  const data = await response.json();

  if (!response.ok) {
    throw new Error(data.error || "Erro ao carregar formulários.");
  }

  return data.guardianForms || [];
};

export const getGuardianForm = async (id, token) => {
  const response = await fetch(`${API_URL}/view/${id}`, {
    headers: {
      Authorization: `Bearer ${token}`,
    },
  });

  const data = await response.json();

  if (!response.ok) {
    throw new Error(data.error || "Formulário não encontrado.");
  }

  return data.guardianForm;
};

export const deleteGuardianForm = async (id, token) => {
  const response = await fetch(`${API_URL}/delete/${id}`, {
    method: "DELETE",
    headers: {
      Authorization: `Bearer ${token}`,
      "Content-Type": "application/json",
    },
  });

  const data = await response.json();

  if (!response.ok) {
    throw new Error(data.error || "Erro ao deletar formulário.");
  }

  return data;
};

export const saveGuardianForm = async (formData, token, id = null) => {
  const isEditMode = !!id;
  const method = isEditMode ? "PUT" : "POST";
  const url = isEditMode ? `${API_URL}/edit/${id}` : `${API_URL}/add`;

  const payload = { ...formData };
  if (isEditMode && !payload.password) {
    delete payload.password;
  }

  const response = await fetch(url, {
    method,
    headers: {
      Authorization: `Bearer ${token}`,
      "Content-Type": "application/json",
    },
    body: JSON.stringify(payload),
  });

  const data = await response.json();

  if (!response.ok) {
    throw new Error(data.error || "Erro ao salvar dados.");
  }

  return data;
};
