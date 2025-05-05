import React, { useState, useEffect } from "react";
import { useNavigate } from "react-router-dom";
import { useAuth } from "../contexts/AuthContext";
import {
  FaUserGraduate,
  FaCalendarAlt,
  FaChalkboardTeacher,
  FaRegClock,
  FaHistory,
} from "react-icons/fa";

import { fetchTodayLessons, fetchLastLessons } from "../api/lessons";

const Dashboard = () => {
  const navigate = useNavigate();
  const { token } = useAuth();
  const [errorMessage, setErrorMessage] = useState("");
  const [todayLessons, setTodayLessons] = useState([]);
  const [lessonHistory, setLessonHistory] = useState([]);
  const [loading, setLoading] = useState(true); // novo estado de loading

  useEffect(() => {
    if (!token) {
      setErrorMessage("Token não encontrado, por favor faça login.");
      navigate("/login");
      return;
    }

    const fetchLessons = async () => {
      setLoading(true); // começa o loading
      try {
        const todayData = await fetchTodayLessons(token);
        const historyData = await fetchLastLessons(token);

        setTodayLessons(todayData);
        setLessonHistory(historyData);
      } catch (error) {
        console.error(error);
        setErrorMessage("Erro ao carregar dados das aulas.");
      } finally {
        setLoading(false); // termina o loading
      }
    };

    fetchLessons();
  }, [token, navigate]);

  if (errorMessage) {
    return (
      <div className="text-center text-red-600 mt-8 text-lg">
        {errorMessage}
      </div>
    );
  }

  if (loading) {
    return (
      <div className="flex items-center justify-center h-screen">
        <div className="animate-spin rounded-full h-16 w-16 border-t-4 border-blue-500 border-solid"></div>
      </div>
    );
  }

  const cards = [
    {
      title: "Gestão de Estudantes",
      description: "Gerencie registros dos estudantes.",
      icon: <FaUserGraduate size={40} className="text-blue-500 mb-4" />,
      buttonClass: "bg-blue-500 hover:bg-blue-600",
      route: "/students",
    },
    {
      title: "Gestão de Agendamentos",
      description: "Visualize os horários das aulas dos estudantes.",
      icon: <FaCalendarAlt size={40} className="text-green-500 mb-4" />,
      buttonClass: "bg-green-500 hover:bg-green-600",
      route: "/lessons",
    },
    {
      title: "Gestão de Professores",
      description: "Visualize e gerencie perfis de professores.",
      icon: <FaChalkboardTeacher size={40} className="text-purple-500 mb-4" />,
      buttonClass: "bg-purple-500 hover:bg-purple-600",
      route: "/teachers",
    },
  ];

  return (
    <div className="w-full h-full mt-16 px-4 sm:px-8 md:px-14 py-6">
      <div className="max-w-7xl mx-auto">
        <h1 className="text-4xl font-bold text-center text-gray-800">
          Painel Gerencial
        </h1>

        <div className="p-10">
          <div className="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-8">
            {cards.map((card, index) => (
              <div
                key={index}
                className="bg-white border border-gray-200 rounded-lg p-6 flex flex-col items-center text-center hover:shadow-xl transition duration-300"
              >
                {card.icon}
                <h3 className="text-lg font-bold text-gray-800 mb-2">
                  {card.title}
                </h3>
                <p className="text-gray-500 text-sm mb-6">{card.description}</p>
                <button
                  className={`${card.buttonClass} text-white py-2 px-5 rounded-full transition`}
                  onClick={() => navigate(card.route)}
                >
                  Acessar
                </button>
              </div>
            ))}
          </div>

          {/* Tabelas */}
          <div className="mt-12">
            <div className="grid grid-cols-1 sm:grid-cols-2 gap-8">
              {/* Tabela Aulas do Dia */}
              <div className="bg-white border border-gray-200 rounded-lg p-6">
                <div className="flex items-center mb-4">
                  <FaRegClock size={24} className="text-blue-500 mr-2" />
                  <h3 className="text-xl font-bold text-gray-800">
                    Aulas do Dia
                  </h3>
                </div>
                <div className="overflow-x-auto">
                  <table className="w-full table-auto border-collapse">
                    <thead>
                      <tr>
                        <th className="border-b py-2 px-4 text-left">Hora</th>
                        <th className="border-b py-2 px-4 text-left">
                          Estudante
                        </th>
                        <th className="border-b py-2 px-4 text-left">
                          Professor
                        </th>
                        <th className="border-b py-2 px-4 text-left">
                          Disciplina
                        </th>
                      </tr>
                    </thead>
                    <tbody>
                      {todayLessons.map((lesson, index) => (
                        <tr key={index}>
                          <td className="border-b py-2 px-4">
                            {new Date(lesson.lesson_date).toLocaleTimeString(
                              [],
                              { hour: "2-digit", minute: "2-digit" }
                            )}
                          </td>
                          <td className="border-b py-2 px-4">
                            {lesson.student?.first_name}{" "}
                            {lesson.student?.last_name} -{" "}
                            <a
                              href={`https://wa.me/55${lesson.student?.whatsapp}`}
                              target="_blank"
                              rel="noopener noreferrer"
                              className="text-green-500 underline"
                            >
                              WhatsApp
                            </a>
                          </td>

                          <td className="border-b py-2 px-4">
                            {lesson.teacher?.first_name}{" "}
                            {lesson.teacher?.last_name}
                          </td>
                          <td className="border-b py-2 px-4">
                            {lesson.teacher?.specialty}
                          </td>
                        </tr>
                      ))}
                    </tbody>
                  </table>
                </div>
              </div>

              {/* Tabela Histórico das Últimas Aulas */}
              <div className="bg-white border border-gray-200 rounded-lg p-6">
                <div className="flex items-center mb-4">
                  <FaHistory size={24} className="text-purple-500 mr-2" />
                  <h3 className="text-xl font-bold text-gray-800">
                    Histórico das Últimas Aulas
                  </h3>
                </div>
                <div className="overflow-x-auto">
                  <table className="w-full table-auto border-collapse">
                    <thead>
                      <tr>
                        <th className="border-b py-2 px-4 text-left">Data</th>
                        <th className="border-b py-2 px-4 text-left">Hora</th>
                        <th className="border-b py-2 px-4 text-left">
                          Estudante
                        </th>
                        <th className="border-b py-2 px-4 text-left">
                          Professor
                        </th>
                      </tr>
                    </thead>
                    <tbody>
                      {lessonHistory.map((lesson, index) => (
                        <tr key={index}>
                          <td className="border-b py-2 px-4">
                            {new Date(lesson.lesson_date).toLocaleDateString()}
                          </td>
                          <td className="border-b py-2 px-4">
                            {new Date(lesson.lesson_date).toLocaleTimeString(
                              [],
                              { hour: "2-digit", minute: "2-digit" }
                            )}
                          </td>
                          <td className="border-b py-2 px-4">
                            {lesson.student?.first_name}{" "}
                            {lesson.student?.last_name} -{" "}
                            <a
                              href={`https://wa.me/55${lesson.student?.whatsapp}`}
                              target="_blank"
                              rel="noopener noreferrer"
                              className="text-green-500 underline"
                            >
                              WhatsApp
                            </a>
                          </td>

                          <td className="border-b py-2 px-4">
                            {lesson.teacher?.first_name}{" "}
                            {lesson.teacher?.last_name}
                          </td>
                        </tr>
                      ))}
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
};

export default Dashboard;
