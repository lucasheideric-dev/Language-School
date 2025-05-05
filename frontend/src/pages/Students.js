import React, { useState, useEffect } from "react";
import { useNavigate } from "react-router-dom";

import { useAuth } from "../contexts/AuthContext";

import StudentForm from "../components/Forms/StudentForm";
import Modal from "../components/Modal";
import Table from "../components/Table";

import { fetchStudents, deleteStudent } from "../api/students";

import Swal from "sweetalert2";

const Students = () => {
  const [students, setStudents] = useState([]);
  const [errorMessage, setErrorMessage] = useState("");
  const [loading, setLoading] = useState(true);
  const [selectedStudentId, setSelectedStudentId] = useState(null);
  const [modalVisible, setModalVisible] = useState(false);
  const [editMode, setEditMode] = useState(false);
  const navigate = useNavigate();

  const { token } = useAuth();

  const loadStudents = async () => {
    try {
      const data = await fetchStudents(token);
      setStudents(data);
    } catch (error) {
      setErrorMessage(error.message || "Error loading students.");
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
        await deleteStudent(id, token);
        loadStudents();
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
    loadStudents();
  }, [token]);

  const openModal = (teacherId = null) => {
    setSelectedStudentId(teacherId);
    setEditMode(!!teacherId);
    setModalVisible(true);
  };

  const closeModal = () => {
    setSelectedStudentId(null);
    setEditMode(false);
    setModalVisible(false);
    loadStudents();
  };

  const colunas = [
    { chave: "first_name", titulo: "Nome" },
    { chave: "last_name", titulo: "Sobrenome" },
    { chave: "birth_date", titulo: "Data de Nascimento" },
    { chave: "whatsapp", titulo: "WhatsApp" },
    { chave: "cpf", titulo: "CPF" },
  ];

  if (loading) return <div className="p-6">Carregando alunos...</div>;

  return (
    <div className="w-full h-hull mt-16 px-14 py-6">
      <div className="max-w-7xl mx-auto">
        <div className="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
          <h1 className="text-3xl md:text-4xl font-bold text-gray-800">
            Lista de Alunos
          </h1>

          <button
            onClick={() => openModal()}
            className="bg-[#002A51] hover:bg-[#00203f] text-white font-semibold px-5 py-2 rounded-lg shadow-md transition duration-300"
          >
            + Adicionar Aluno
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
            dados={students}
            chaveUnica="id"
            ordenacaoInicial={{ coluna: "first_name", ordem: "asc" }}
            onEditar={openModal}
            onExcluir={handleDelete}
          />
        </div>
      </div>

      {modalVisible && (
        <Modal onClose={closeModal}>
          <StudentForm id={selectedStudentId} onClose={closeModal} />
        </Modal>
      )}
    </div>
  );
};

export default Students;
