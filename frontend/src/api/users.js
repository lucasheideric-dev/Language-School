const API_URL = "http://127.0.0.1:8000/api/users";

export const fetchUsers = async (token) => {
  const response = await fetch(API_URL, {
    headers: {
      Authorization: `Bearer ${token}`,
      "Content-Type": "application/json",
    },
  });

  const data = await response.json();

  if (!response.ok) {
    throw new Error(data.error || "Erro ao carregar usuários.");
  }

  return data.users || [];
};

export const getUser = async (id, token) => {
  const response = await fetch(`${API_URL}/view/${id}`, {
    headers: {
      Authorization: `Bearer ${token}`,
    },
  });

  const data = await response.json();

  if (!response.ok) {
    throw new Error(data.error || "Usuário não encontrado.");
  }

  return data.user;
};

export const deleteUser = async (id, token) => {
  const response = await fetch(`${API_URL}/delete/${id}`, {
    method: "DELETE",
    headers: {
      Authorization: `Bearer ${token}`,
      "Content-Type": "application/json",
    },
  });

  const data = await response.json();

  if (!response.ok) {
    throw new Error(data.error || "Erro ao deletar usuário.");
  }

  return data;
};

export const saveUser = async (formData, token, id = null) => {
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
