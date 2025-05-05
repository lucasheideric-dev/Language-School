import React, { useState, useEffect } from "react";
import { useNavigate } from "react-router-dom";
import { useAuth } from "../contexts/AuthContext";
import LessonForm from "../components/Forms/LessonForm";
import Modal from "../components/Modal";
import Table from "../components/Table";
import {
  fetchLessons,
  deleteLesson,
  finishLesson,
  cancelLesson,
  playLesson,
} from "../api/lessons";
import Swal from "sweetalert2";

const Lessons = () => {
  const [lessons, setLessons] = useState([]);
  const [errorMessage, setErrorMessage] = useState("");
  const [loading, setLoading] = useState(true);
  const [selectedLessonId, setSelectedLessonId] = useState(null);
  const [modalVisible, setModalVisible] = useState(false);
  const [editMode, setEditMode] = useState(false);
  const navigate = useNavigate();
  const { token } = useAuth();

  const carregarAulas = async () => {
    try {
      if (!token) {
        setErrorMessage("Token não encontrado, por favor faça login.");
        navigate("/login");
        return;
      }

      const data = await fetchLessons(token);

      const aulasFormatadas = data.map((lesson) => ({
        ...lesson,
        student_id: lesson.student?.first_name || "Estudante não encontrado",
        teacher_id: lesson.teacher?.first_name || "Professor não encontrado",
        whatsapp: lesson.student?.whatsapp || "WhatsApp não encontrado",
      }));

      setLessons(aulasFormatadas);
    } catch (error) {
      setErrorMessage(error.message || "Erro ao carregar agendamentos.");
      navigate("/login");
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    carregarAulas();
  }, [token]);

  const handleFinalizar = async (id) => {
    const result = await Swal.fire({
      title: "Deseja finalizar?",
      text: "Esta aula será marcada como finalizada.",
      icon: "info",
      showCancelButton: true,
      cancelButtonColor: "#d33",
      confirmButtonColor: "#002A51",
      confirmButtonText: "Sim, finalizar",
      cancelButtonText: "Cancelar",
      reverseButtons: true,
    });

    if (result.isConfirmed) {
      try {
        await finishLesson(id, token);
        carregarAulas();
      } catch (error) {
        Swal.fire(
          "Erro!",
          error.message || "Erro ao finalizar agendamento.",
          "error"
        );
      }
    }
  };

  const handleCancelar = async (id) => {
    const result = await Swal.fire({
      title: "Deseja cancelar?",
      text: "Esta aula será marcada como cancelada.",
      icon: "info",
      showCancelButton: true,
      cancelButtonColor: "#d33",
      confirmButtonColor: "#002A51",
      confirmButtonText: "Sim, cancelar",
      cancelButtonText: "Cancelar",
      reverseButtons: true,
    });

    if (result.isConfirmed) {
      try {
        await cancelLesson(id, token);
        carregarAulas();
      } catch (error) {
        Swal.fire(
          "Erro!",
          error.message || "Erro ao cancelar agendamento.",
          "error"
        );
      }
    }
  };

  const handleStart = async (id) => {
    const result = await Swal.fire({
      title: "Deseja iniciar?",
      text: "Esta aula será marcada como iniciada.",
      icon: "info",
      showCancelButton: true,
      cancelButtonColor: "#d33",
      confirmButtonColor: "#002A51",
      confirmButtonText: "Sim, iniciar",
      cancelButtonText: "Cancelar",
      reverseButtons: true,
    });

    if (result.isConfirmed) {
      try {
        await playLesson(id, token);
        carregarAulas();
      } catch (error) {
        Swal.fire(
          "Erro!",
          error.message || "Erro ao cancelar agendamento.",
          "error"
        );
      }
    }
  };

  const handleDelete = async (id) => {
    const result = await Swal.fire({
      title: "Tem certeza?",
      text: "Essa ação não poderá ser desfeita!",
      icon: "warning",
      showCancelButton: true,
      cancelButtonColor: "#d33",
      confirmButtonColor: "#002A51",
      confirmButtonText: "Sim, excluir!",
      cancelButtonText: "Cancelar",
      reverseButtons: true,
    });

    if (result.isConfirmed) {
      try {
        await deleteLesson(id, token);
        carregarAulas();
      } catch (error) {
        Swal.fire(
          "Erro!",
          error.message || "Erro ao excluir agendamento.",
          "error"
        );
      }
    }
  };

  const openModal = (userId = null) => {
    setSelectedLessonId(userId);
    setEditMode(!!userId);
    setModalVisible(true);
  };

  const closeModal = () => {
    setModalVisible(false);
    setSelectedLessonId(null);
    setEditMode(false);
    carregarAulas();
  };

  const colunas = [
    { chave: "content", titulo: "Conteúdo" },
    { chave: "status", titulo: "Status" },
    { chave: "lesson_date", titulo: "Data" },
    { chave: "student_id", titulo: "Estudante" },
    { chave: "teacher_id", titulo: "Professor" },
    { chave: "whatsapp", titulo: "WhatsApp" },
  ];

  if (loading) return <div className="p-6">Carregando agendamentos...</div>;

  return (
    <div className="w-full h-full mt-16 px-14 py-6">
      <div className="max-w-7xl mx-auto">
        <div className="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
          <h1 className="text-3xl md:text-4xl font-bold text-gray-800">
            Controle de Agendamentos
          </h1>
          <button
            onClick={() => openModal()}
            className="bg-[#002A51] hover:bg-[#00203f] text-white font-semibold px-5 py-2 rounded-lg shadow-md transition duration-300"
          >
            + Adicionar Agendamento
          </button>
        </div>

        {errorMessage && (
          <div className="bg-red-100 text-red-700 px-4 py-3 rounded mb-4 shadow">
            {errorMessage}
          </div>
        )}

        <div className="bg-white rounded-sm shadow overflow-x-auto">
          <Table
            colunas={colunas}
            dados={lessons}
            chaveUnica="id"
            ordenacaoInicial={{ coluna: "lesson_date", ordem: "desc" }}
            onEditar={openModal}
            onExcluir={handleDelete}
            contexto="lessons"
            onIniciar={handleStart}
            onFinalizar={handleFinalizar}
            onCancelar={handleCancelar}
          />
        </div>
      </div>

      {modalVisible && (
        <Modal onClose={closeModal}>
          <LessonForm id={selectedLessonId} onClose={closeModal} />
        </Modal>
      )}
    </div>
  );
};

export default Lessons;
