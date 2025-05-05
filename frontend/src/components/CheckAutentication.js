import { Navigate, Outlet } from "react-router-dom";

const CheckAutentication = () => {
  const localToken = localStorage.getItem("token");
  const sessionToken = sessionStorage.getItem("token");

  const token = localToken || sessionToken;

  if (!token) {
    return <Navigate to="/login" replace />;
  }

  return <Outlet />;
};

export default CheckAutentication;
