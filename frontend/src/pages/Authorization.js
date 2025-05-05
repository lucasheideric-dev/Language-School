import React, { useEffect, useState, useRef } from "react";
import { useNavigate } from "react-router-dom";
import { useAuth } from "../contexts/AuthContext";
import { getTeacher } from "../api/teachers";
import { getStudent } from "../api/students";
import { jsPDF } from "jspdf";
import html2canvas from "html2canvas";
import { saveGuardianForm } from "../api/guardianForms";

const Authorization = () => {
  const [aluno, setAluno] = useState(null);
  const [professor, setProfessor] = useState(null);
  const [dataAula, setDataAula] = useState(null);
  const { token } = useAuth();
  const navigate = useNavigate();
  const buttonRef = useRef(null);
  const [lessonId, setLessonId] = useState(null);

  useEffect(() => {
    const params = new URLSearchParams(window.location.search);
    const alunoId = params.get("alunoId");
    const professorId = params.get("professorId");
    const dataAula = params.get("dataAula");
    const lessonId = params.get("lessonId");

    setLessonId(lessonId);

    if (!token) {
      navigate("/login");
      return;
    }

    const fetchData = async () => {
      try {
        const studentResponse = await getStudent(alunoId, token);
        const teacherResponse = await getTeacher(professorId, token);

        setAluno(studentResponse);
        setProfessor(teacherResponse);
        setDataAula(dataAula);
      } catch (error) {
        console.error("Erro ao buscar dados", error);
        navigate("/login");
      }
    };

    fetchData();
  }, [token, navigate]);

  const handleGeneratePDF = async () => {
    if (buttonRef.current) {
      buttonRef.current.style.display = "none";
    }

    const input = document.getElementById("pdf-content");

    html2canvas(input, {
      scale: 2,
      useCORS: true,
      logging: false,
      x: 0,
      y: 0,
    }).then((canvas) => {
      const imgData = canvas.toDataURL("image/png");
      const doc = new jsPDF("p", "mm", "a4");

      doc.addImage(imgData, "PNG", 10, 10, 190, 0);

      doc.save("autorizacao.pdf");

      if (lessonId) {
        const formData = {
          lesson_id: lessonId,
          aluno_id: aluno?.id,
          professor_id: professor?.id,
          data_aula: dataAula,
        };

        saveGuardianForm(formData, token)
          .then((response) => {})
          .catch((error) => {
            console.error("Erro ao salvar o formulário", error);
          });
      } else {
        console.error("lessonId não encontrado.");
      }

      if (buttonRef.current) {
        buttonRef.current.style.display = "inline-block";
      }
    });
  };

  return (
    <div
      className="max-w-3xl mx-auto p-6 bg-white shadow-lg rounded-lg mt-10"
      id="pdf-content"
    >
      <h1 className="text-2xl font-semibold text-center text-gray-800 mb-6">
        Requisição de Autorização
      </h1>

      {aluno && (
        <div className="mb-6">
          <h2 className="text-xl font-semibold text-gray-700">
            Informações do Aluno
          </h2>
          <p className="text-gray-600">
            <strong>Nome:</strong> {aluno.first_name} {aluno.last_name}
          </p>
          <p className="text-gray-600">
            <strong>Data Nascimento:</strong> {aluno.birth_date}
          </p>
          <p className="text-gray-600">
            <strong>CPF:</strong> {aluno.cpf}
          </p>
        </div>
      )}

      {professor && (
        <div className="mb-6">
          <h2 className="text-xl font-semibold text-gray-700">
            Informações do Professor
          </h2>
          <p className="text-gray-600">
            <strong>Nome:</strong> {professor.first_name} {professor.last_name}
          </p>
        </div>
      )}

      {dataAula && (
        <div className="mb-6">
          <h2 className="text-xl font-semibold text-gray-700">
            Informações da Aula
          </h2>
          <p className="text-gray-600">
            <strong>Data da Aula:</strong> {dataAula}
          </p>
        </div>
      )}

      <div className="mb-6 text-center mt-5">
        <h2 className="text-xl font-semibold text-gray-700">
          Assinatura do Responsável
        </h2>
        <br />
        <p>___________________________________________________</p>
      </div>

      <div className="flex justify-center mt-8">
        <button
          ref={buttonRef}
          onClick={handleGeneratePDF}
          className="bg-[#002A51] hover:bg-[#00203f] text-white font-semibold px-6 py-3 rounded-lg shadow-md transition duration-300"
        >
          Gerar PDF
        </button>
      </div>
    </div>
  );
};

export default Authorization;
