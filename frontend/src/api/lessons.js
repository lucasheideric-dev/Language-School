const API_URL = "http://127.0.0.1:8000/api/lessons";

export const fetchLessons = async (token) => {
  const response = await fetch(API_URL, {
    headers: {
      Authorization: `Bearer ${token}`,
      "Content-Type": "application/json",
    },
  });

  const data = await response.json();

  if (!response.ok) {
    throw new Error(data.error || "Erro ao carregar aulas.");
  }

  return data.lessons || [];
};

export const fetchTodayLessons = async (token) => {
  const response = await fetch(`${API_URL}/today`, {
    headers: {
      Authorization: `Bearer ${token}`,
      "Content-Type": "application/json",
    },
  });

  const data = await response.json();

  if (!response.ok) {
    throw new Error(data.error || "Erro ao carregar aulas de hoje.");
  }

  return data.lessons || [];
};

export const fetchLastLessons = async (token) => {
  const response = await fetch(`${API_URL}/lastLessons`, {
    headers: {
      Authorization: `Bearer ${token}`,
      "Content-Type": "application/json",
    },
  });

  const data = await response.json();

  if (!response.ok) {
    throw new Error(data.error || "Erro ao carregar últimas aulas.");
  }

  return data.lessons || [];
};

export const getLesson = async (id, token) => {
  const response = await fetch(`${API_URL}/view/${id}`, {
    headers: {
      Authorization: `Bearer ${token}`,
    },
  });

  const data = await response.json();

  if (!response.ok) {
    throw new Error(data.error || "Aula não encontrada.");
  }

  return data.lesson;
};

export const deleteLesson = async (id, token) => {
  const response = await fetch(`${API_URL}/delete/${id}`, {
    method: "DELETE",
    headers: {
      Authorization: `Bearer ${token}`,
      "Content-Type": "application/json",
    },
  });

  const data = await response.json();

  if (!response.ok) {
    throw new Error(data.error || "Erro ao deletar aula.");
  }

  return data;
};

export const saveLesson = async (formData, token, id = null) => {
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
    throw new Error(data.error || "Erro ao salvar aula.");
  }

  return data;
};

export const finishLesson = async (id, token) => {
  const response = await fetch(`${API_URL}/finishLesson/${id}`, {
    headers: {
      Authorization: `Bearer ${token}`,
    },
  });

  const data = await response.json();

  if (!response.ok) {
    throw new Error(data.error || "Aula não encontrada.");
  }

  return data.lesson;
};

export const cancelLesson = async (id, token) => {
  const response = await fetch(`${API_URL}/cancelLesson/${id}`, {
    headers: {
      Authorization: `Bearer ${token}`,
    },
  });

  const data = await response.json();

  if (!response.ok) {
    throw new Error(data.error || "Aula não encontrada.");
  }

  return data.lesson;
};

export const playLesson = async (id, token) => {
  const response = await fetch(`${API_URL}/playLesson/${id}`, {
    headers: {
      Authorization: `Bearer ${token}`,
    },
  });

  const data = await response.json();

  if (!response.ok) {
    throw new Error(data.error || "Aula não encontrada.");
  }

  return data.lesson;
};
