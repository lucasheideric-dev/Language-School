const API_URL = "http://127.0.0.1:8000/api/teachers";

export const fetchTeachers = async (token) => {
  const response = await fetch(API_URL, {
    headers: {
      Authorization: `Bearer ${token}`,
      "Content-Type": "application/json",
    },
  });

  const data = await response.json();

  if (!response.ok) {
    throw new Error(data.error || "Erro ao carregar funções.");
  }

  return data.teachers || [];
};

export const fecthListTeachers = async (token) => {
  const response = await fetch(`${API_URL}/list`, {
    headers: {
      Authorization: `Bearer ${token}`,
      "Content-Type": "application/json",
    },
  });

  const data = await response.json();

  if (!response.ok) {
    throw new Error(data.error || "Erro ao carregar professores.");
  }

  return data.teachers || [];
};

export const getTeacher = async (id, token) => {
  const response = await fetch(`${API_URL}/view/${id}`, {
    headers: {
      Authorization: `Bearer ${token}`,
    },
  });

  const data = await response.json();

  if (!response.ok) {
    throw new Error(data.error || "Função não encontrada.");
  }

  return data.teacher;
};

export const deleteTeacher = async (id, token) => {
  const response = await fetch(`${API_URL}/delete/${id}`, {
    method: "DELETE",
    headers: {
      Authorization: `Bearer ${token}`,
      "Content-Type": "application/json",
    },
  });

  const data = await response.json();

  if (!response.ok) {
    throw new Error(data.error || "Erro ao deletar função.");
  }

  return data;
};

export const saveTeacher = async (formData, token, id = null) => {
  const isEditMode = !!id;
  const method = isEditMode ? "PUT" : "POST";
  const url = isEditMode ? `${API_URL}/edit/${id}` : `${API_URL}/add`;

  const payload = { ...formData };

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
    throw new Error(data.error || "Erro ao salvar função.");
  }

  return data;
};
