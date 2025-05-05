import React, { useState, useEffect } from "react";
import { getLesson, saveLesson } from "../../api/lessons";
import { fecthListTeachers } from "../../api/teachers";
import { fecthListStudents } from "../../api/students";
import Swal from "sweetalert2";

import { toast } from "react-toastify";

const LessonForm = ({ id, onClose }) => {
  const isEditMode = !!id;
  const [formData, setFormData] = useState({
    lesson_date: "",
    teacher_id: "",
    student_id: "",
    lesson: "",
    content: "",
    status: false,
  });

  const [error, setError] = useState("");
  const [loading, setLoading] = useState(false);
  const [teachers, setTeachers] = useState([]);
  const [students, setStudents] = useState([]);

  const token = sessionStorage.getItem("token");

  useEffect(() => {
    const fetchLesson = async () => {
      setLoading(true);
      try {
        const lesson = await getLesson(id, token);
        if (lesson) {
          setFormData({
            lesson_date: lesson.lesson_date
              ? lesson.lesson_date.slice(0, 16)
              : "",
            student_id: lesson.student_id || "",
            teacher_id: lesson.teacher_id || "",
            lesson: lesson.lesson || "",
            content: lesson.content || "",
            status: lesson.status || false,
          });
        } else {
          setError("Agendamento não encontrado.");
        }
      } catch (err) {
        setError(err.message || "Erro ao carregar dados do agendamento.");
      } finally {
        setLoading(false);
      }
    };

    if (isEditMode) fetchLesson();
  }, [id, token]);

  useEffect(() => {
    const fetchData = async () => {
      try {
        const [studentsData, teachersData] = await Promise.all([
          fecthListStudents(token),
          fecthListTeachers(token),
        ]);

        const formattedStudents = Object.entries(studentsData).map(
          ([id, student]) => ({
            id,
            name: student,
          })
        );

        const formattedTeachers = Object.entries(teachersData).map(
          ([id, teacher]) => ({
            id,
            name: teacher,
          })
        );

        setStudents(formattedStudents || []);
        setTeachers(formattedTeachers || []);
      } catch (err) {
        console.error("Erro ao carregar estudantes ou professores", err);
      }
    };

    fetchData();
  }, [token]);

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
      const response = await saveLesson(formData, token, id);

      isEditMode
        ? toast.success(`Agendamento atualizado com sucesso.`)
        : toast.success(`Agendamento cadastrado com sucesso.`);

      if (response.minor) {
        onClose();

        Swal.fire({
          title: "Aluno menor de idade!",
          text: response.message || "É necessário gerar uma requisição.",
          icon: "warning",
          showCancelButton: true,
          confirmButtonText: "Gerar Requisição",
          cancelButtonText: "Cancelar",
          reverseButtons: true,
        }).then((result) => {
          if (result.isConfirmed) {
            const queryParams = new URLSearchParams({
              alunoId: response.student_id,
              professorId: response.teacher_id,
              lessonId: response.lesson_id,
              dataAula: response.lesson_date
                ? new Date(response.lesson_date).toLocaleDateString("pt-BR")
                : "",
            });

            window.location.href = `/authorization?${queryParams.toString()}`;
          }
        });

        return;
      }

      onClose();
    } catch (err) {
      setError(err.message || "Erro ao salvar dados.");
    }
  };

  return (
    <div>
      <h2 className="text-xl font-bold mb-4">
        {isEditMode ? "Editar Aula" : "Adicionar Aula"}
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
          {/* Data da aula */}
          <div className="mb-4">
            <label className="block text-gray-700 mb-2" htmlFor="lesson_date">
              Data e Hora da Aula *
            </label>
            <input
              type="datetime-local"
              name="lesson_date"
              value={formData.lesson_date}
              onChange={handleChange}
              required
              className="border border-gray-300 rounded px-3 py-2 w-full"
            />
          </div>

          {/* Estudante */}
          <div className="mb-4 relative">
            <label className="block text-gray-700 mb-2" htmlFor="student_id">
              Estudante *
            </label>
            <div className="relative">
              <select
                name="student_id"
                value={formData.student_id}
                onChange={handleChange}
                required
                className="border border-gray-300 rounded-lg pl-3 pr-10 py-2 w-full focus:outline-none focus:ring-2 appearance-none"
              >
                <option value="">Selecione um estudante</option>
                {students.map((student) => (
                  <option key={student.id} value={student.id}>
                    {student.name}
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

          {/* Professor */}
          <div className="mb-4 relative">
            <label className="block text-gray-700 mb-2" htmlFor="teacher_id">
              Professor *
            </label>
            <div className="relative">
              <select
                name="teacher_id"
                value={formData.teacher_id}
                onChange={handleChange}
                required
                className="border border-gray-300 rounded-lg pl-3 pr-10 py-2 w-full focus:outline-none focus:ring-2 appearance-none"
              >
                <option value="">Selecione um professor</option>
                {teachers.map((teacher) => (
                  <option key={teacher.id} value={teacher.id}>
                    {teacher.name}
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

          {/* Conteúdo */}
          <div className="mb-4">
            <label className="block text-gray-700 mb-2" htmlFor="content">
              Conteúdo *
            </label>
            <textarea
              name="content"
              value={formData.content}
              onChange={handleChange}
              required
              className="border border-gray-300 rounded px-3 py-2 w-full"
              rows="4"
            />
          </div>

          {/* Status */}
          <div className="mb-4 relative">
            <label className="block text-gray-700 mb-2" htmlFor="status">
              Status da Aula
            </label>
            <div className="relative">
              <select
                name="status"
                value={formData.status}
                onChange={handleChange}
                className="border border-gray-300 rounded-lg pl-3 pr-10 py-2 w-full focus:outline-none focus:ring-2 appearance-none"
              >
                <option value="">Selecione o status</option>
                <option value="Agendada">Agendada</option>
                <option value="Em andamento">Em andamento</option>
                <option value="Cancelada">Cancelada</option>
                <option value="Finalizada">Finalizada</option>
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

export default LessonForm;
