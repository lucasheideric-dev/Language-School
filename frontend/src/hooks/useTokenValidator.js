import { useEffect } from "react";
import Swal from "sweetalert2";
import { useNavigate, useLocation } from "react-router-dom";

const useTokenValidator = () => {
  const navigate = useNavigate();
  const location = useLocation();

  useEffect(() => {
    const interval = setInterval(() => {
      const token =
        sessionStorage.getItem("token") || localStorage.getItem("token");

      if (!token && location.pathname !== "/login") {
        Swal.fire({
          icon: "warning",
          title: "Sessão expirada",
          text: "Sua sessão expirou. Você será redirecionado para a tela de login.",
          allowOutsideClick: true,
          allowEscapeKey: false,
          confirmButtonText: "Reconectar",
          confirmButtonColor: "#002A51",
        }).then(() => {
          navigate("/login");
        });
      }
    }, 30000);

    return () => clearInterval(interval);
  }, [navigate, location]);
};

export default useTokenValidator;
