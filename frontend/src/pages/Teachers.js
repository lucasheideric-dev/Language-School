import React, { useState, useEffect } from "react";
import { useNavigate } from "react-router-dom";

import { useAuth } from "../contexts/AuthContext";

import TeacherForm from "../components/Forms/TeacherForm";
import Modal from "../components/Modal";
import Table from "../components/Table";

import { fetchTeachers, deleteTeacher } from "../api/teachers";

import Swal from "sweetalert2";

const Teachers = () => {
  const [teachers, setTeachers] = useState([]);
  const [errorMessage, setErrorMessage] = useState("");
  const [loading, setLoading] = useState(true);
  const [selectedTeacherId, setSelectedTeacherId] = useState(null);
  const [modalVisible, setModalVisible] = useState(false);
  const [editMode, setEditMode] = useState(false);
  const navigate = useNavigate();

  const { token } = useAuth();

  const loadTeachers = async () => {
    try {
      const data = await fetchTeachers(token);
      setTeachers(data);
    } catch (error) {
      setErrorMessage(error.message || "Error loading teachers.");
      navigate("/login");
    } finally {
      setLoading(false);
    }
  };

  const handleDelete = async (id) => {
    const result = await Swal.fire({
      title: "Tem certeza?",
      text: "Esta ação não pode ser desfeita!",
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
        await deleteTeacher(id, token);
        loadTeachers();
      } catch (error) {
        Swal.fire(
          "Error!",
          error.message || "Erro ao deletar a função",
          "error"
        );
      }
    }
  };

  useEffect(() => {
    loadTeachers();
  }, [token]);

  const openModal = (teacherId = null) => {
    setSelectedTeacherId(teacherId);
    setEditMode(!!teacherId);
    setModalVisible(true);
  };

  const closeModal = () => {
    setSelectedTeacherId(null);
    setEditMode(false);
    setModalVisible(false);
    loadTeachers();
  };

  const colunas = [
    { chave: "first_name", titulo: "Nome" },
    { chave: "last_name", titulo: "Sobrenome" },
    { chave: "birth_date", titulo: "Data de Nascimento" },
    { chave: "specialty", titulo: "Especialidade" },
    { chave: "status", titulo: "Status" },
    { chave: "cpf", titulo: "CPF" },
  ];

  if (loading) return <div className="p-6">Carregando professores...</div>;

  return (
    <div className="w-full h-hull mt-16 px-14 py-6">
      <div className="max-w-7xl mx-auto">
        <div className="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
          <h1 className="text-3xl md:text-4xl font-bold text-gray-800">
            Lista de Professores
          </h1>

          <button
            onClick={() => openModal()}
            className="bg-[#002A51] hover:bg-[#00203f] text-white font-semibold px-5 py-2 rounded-lg shadow-md transition duration-300"
          >
            + Adicionar Professor
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
            dados={teachers}
            chaveUnica="id"
            ordenacaoInicial={{ coluna: "first_name", ordem: "asc" }}
            onEditar={openModal}
            onExcluir={handleDelete}
          />
        </div>
      </div>

      {modalVisible && (
        <Modal onClose={closeModal}>
          <TeacherForm id={selectedTeacherId} onClose={closeModal} />
        </Modal>
      )}
    </div>
  );
};

export default Teachers;
