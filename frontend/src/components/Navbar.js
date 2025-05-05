import React, { useState } from "react";
import { Link, useNavigate } from "react-router-dom";
import { FaCode, FaSearch, FaBars, FaTimes } from "react-icons/fa";
import Cookies from "js-cookie";

const Navbar = () => {
  const [searchQuery, setSearchQuery] = useState("");
  const [searchResults, setSearchResults] = useState([]);
  const [menuOpen, setMenuOpen] = useState(false);
  const navigate = useNavigate();

  const usuario = JSON.parse(localStorage.getItem("user"));
  // const isAdmin = usuario?.role_id === 1;

  const suggestions = [
    { label: "Login", path: "/login" },
    { label: "Users", path: "/students" },
    { label: "Perfil", path: "/profile" },
    { label: "Configurações", path: "/settings" },
    { label: "Professores", path: "/teachers" },
    // ...(isAdmin ? [{ label: "Livros", path: "/books" }] : []),
    // ...(isAdmin ? [{ label: "Permissões", path: "/teachers" }] : []),
  ];

  const handleSearchChange = (e) => {
    const query = e.target.value;
    setSearchQuery(query);
    const filtered = suggestions.filter((item) =>
      item.label.toLowerCase().includes(query.toLowerCase())
    );
    setSearchResults(filtered);
  };

  const handleResultClick = (path) => {
    setSearchQuery("");
    setSearchResults([]);
    navigate(path);
    setMenuOpen(false);
  };

  const handleLogout = () => {
    sessionStorage.clear();
    localStorage.clear();
    navigate("/login");
  };

  return (
    <nav className="bg-[#002A51] text-white shadow-md sticky top-0 z-50">
      <div className="max-w-7xl mx-auto px-4 py-4 flex justify-between items-center">
        <Link to="/" className="text-2xl font-bold flex items-center">
          <FaCode className="mr-2 text-orange-400" />
          Developer <span className="ml-1 text-orange-400">Heideric</span>
        </Link>

        {/* Search input (desktop) */}
        <div className="hidden md:flex items-center relative">
          <FaSearch className="absolute left-3 text-gray-400" />
          <input
            type="text"
            placeholder="Buscar páginas..."
            className="pl-10 pr-3 py-2 rounded-md text-sm text-black"
            value={searchQuery}
            onChange={handleSearchChange}
          />
          {searchQuery && searchResults.length > 0 && (
            <ul className="absolute top-12 bg-white text-black w-full rounded-md shadow-md z-10">
              {searchResults.map((result, index) => (
                <li
                  key={index}
                  onClick={() => handleResultClick(result.path)}
                  className="px-4 py-2 hover:bg-gray-200 cursor-pointer text-sm"
                >
                  {result.label}
                </li>
              ))}
            </ul>
          )}
        </div>

        <div className="md:hidden">
          <button onClick={() => setMenuOpen(!menuOpen)}>
            {menuOpen ? <FaTimes /> : <FaBars />}
          </button>
        </div>

        {/* Menu desktop */}
        <ul className="hidden md:flex items-center space-x-6 font-medium">
          <li>
            <Link to="/students" className="hover:text-orange-400">
              Alunos
            </Link>
          </li>
          <li>
            <Link to="/teachers" className="hover:text-orange-400">
              Professores
            </Link>

            {/* {JSON.parse(localStorage.getItem("user"))?.role_id === 1 && (
              <Link to="/teachers" className="hover:text-orange-400">
                Permissões
              </Link>
            )} */}
          </li>
          {/* <li>
            {JSON.parse(localStorage.getItem("user"))?.role_id === 1 && (
              <Link to="/books" className="hover:text-orange-400">
                Livros
              </Link>
            )}
          </li> */}
          <li>
            <button
              onClick={handleLogout}
              className="bg-[#FB923C] text-[#622c00] px-3 py-1 rounded-md hover:bg-[#cf5d00] transition"
            >
              Sair
            </button>
          </li>
        </ul>
      </div>

      {/* Mobile menu */}
      {menuOpen && (
        <div className="md:hidden bg-[#002A51] px-4 pb-4 space-y-3">
          <div className="relative">
            <FaSearch className="absolute left-3 top-3 text-gray-400" />
            <input
              type="text"
              placeholder="Buscar páginas..."
              className="w-full pl-10 pr-3 py-2 rounded-md text-sm text-black"
              value={searchQuery}
              onChange={handleSearchChange}
            />
            {searchQuery && searchResults.length > 0 && (
              <ul className="absolute top-12 left-0 right-0 bg-white text-black rounded-md shadow-md z-10">
                {searchResults.map((result, index) => (
                  <li
                    key={index}
                    onClick={() => handleResultClick(result.path)}
                    className="px-4 py-2 hover:bg-gray-200 cursor-pointer text-sm"
                  >
                    {result.label}
                  </li>
                ))}
              </ul>
            )}
          </div>

          <ul className="space-y-2 text-white text-sm font-medium">
            <li>
              <Link to="/students" onClick={() => setMenuOpen(false)}>
                Alunos
              </Link>
            </li>
            <li>
              {/* {JSON.parse(localStorage.getItem("user"))?.role_id === 1 && (
                <Link to="/teachers" onClick={() => setMenuOpen(false)}>
                  Permissões
                </Link>
              )} */}
              <Link to="/teachers" onClick={() => setMenuOpen(false)}>
                Professores
              </Link>
            </li>
            {/* <li>
              {JSON.parse(localStorage.getItem("user"))?.role_id === 1 && (
                <Link to="/books" onClick={() => setMenuOpen(false)}>
                  Livros
                </Link>
              )}
            </li> */}
            <li>
              <button
                onClick={handleLogout}
                className="w-full bg-red-600 px-3 py-2 rounded-md mt-2"
              >
                Sair
              </button>
            </li>
          </ul>
        </div>
      )}
    </nav>
  );
};

export default Navbar;
