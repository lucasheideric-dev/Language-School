import React from "react";
import { Link } from "react-router-dom";
import { FaCode } from "react-icons/fa";

const NotFound = () => {
  return (
    <div className="flex flex-col items-center justify-center h-screen bg-[#002A51] text-white text-center px-4">
      <div className="max-w-md">
        <h1 className="text-6xl font-extrabold mb-4 text-[#FB923C] animate__animated animate__zoomIn">
          404
        </h1>

        <p className="text-2xl mb-6">
          Ops! Parece que você se perdeu. Não conseguimos encontrar a página.
        </p>

        <div className="flex justify-center mb-12">
          <Link
            to="/"
            className="text-2xl font-semibold flex items-center mb-2 hover:text-orange-400 transition-colors duration-200"
          >
            <FaCode className="mr-2 text-orange-400" />
            Developer <span className="ml-1 text-orange-400">Heideric</span>
          </Link>
        </div>

        <Link
          to="/"
          className="bg-[#333] text-white px-6 py-3 rounded-lg shadow-md hover:bg-[#FB923C] transition-all transform hover:scale-105"
        >
          Voltar para o início
        </Link>
      </div>
    </div>
  );
};

export default NotFound;
