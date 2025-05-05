import { Link } from "react-router-dom";
import { FaCode } from "react-icons/fa";

const Footer = () => {
  return (
    <footer className="bg-[#001c37] text-white py-6">
      <div className="container mx-auto px-4 flex flex-col items-center">
        <Link
          to="/"
          className="text-2xl font-semibold flex items-center mb-2 hover:text-orange-400 transition-colors duration-200"
        >
          <FaCode className="mr-2 text-orange-400" />
          Developer <span className="ml-1 text-orange-400">Heideric</span>
        </Link>
        <p className="text-sm text-gray-400">
          &copy; {new Date().getFullYear()} Todos os direitos reservados.
        </p>
      </div>
    </footer>
  );
};

export default Footer;
