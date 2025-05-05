const API_URL = "http://127.0.0.1:8000/api/students";

export const fetchStudents = async (token) => {
  const response = await fetch(API_URL, {
    headers: {
      Authorization: `Bearer ${token}`,
      "Content-Type": "application/json",
    },
  });

  const data = await response.json();

  if (!response.ok) {
    throw new Error(data.error || "Erro ao carregar alunos.");
  }

  return data.students || [];
};

export const fecthListStudents = async (token) => {
  const response = await fetch(`${API_URL}/list`, {
    headers: {
      Authorization: `Bearer ${token}`,
      "Content-Type": "application/json",
    },
  });

  const data = await response.json();

  if (!response.ok) {
    throw new Error(data.error || "Erro ao carregar alunos.");
  }

  return data.students || [];
};

export const getStudent = async (id, token) => {
  const response = await fetch(`${API_URL}/view/${id}`, {
    headers: {
      Authorization: `Bearer ${token}`,
    },
  });

  const data = await response.json();

  if (!response.ok) {
    throw new Error(data.error || "Aluno não encontrada.");
  }

  return data.student;
};

export const deleteStudent = async (id, token) => {
  const response = await fetch(`${API_URL}/delete/${id}`, {
    method: "DELETE",
    headers: {
      Authorization: `Bearer ${token}`,
      "Content-Type": "application/json",
    },
  });

  const data = await response.json();

  if (!response.ok) {
    throw new Error(data.error || "Erro ao deletar aluno.");
  }

  return data;
};

export const saveStudent = async (formData, token, id = null) => {
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
    throw new Error(data.error || "Erro ao salvar aluno.");
  }

  return data;
};
