import {
  BrowserRouter,
  Routes,
  Route,
  Navigate,
  useLocation,
} from "react-router-dom";

import { AuthProvider } from "./contexts/AuthContext";

import CheckAutentication from "./components/CheckAutentication";
import Navbar from "./components/Navbar";
import Footer from "./components/Footer";
import UserForm from "./components/Forms/UserForm";
import SessionProgressBar from "./components/SessionProgressBar";

import Login from "./pages/Login";
import Lessons from "./pages/Lessons";
import Students from "./pages/Students";
import Teachers from "./pages/Teachers";
import Dashboard from "./pages/Dashboard";
import Authorization from "./pages/Authorization";
import NotFound from "./pages/NotFound";

import useTokenValidator from "./hooks/useTokenValidator";

import { ToastContainer } from "react-toastify";
import "react-toastify/dist/ReactToastify.css";

function App() {
  return (
    <BrowserRouter>
      <AuthProvider>
        <ToastContainer />
        <AppContent />
      </AuthProvider>
    </BrowserRouter>
  );
}

const AppContent = () => {
  const location = useLocation();
  const isLoginPage = location.pathname === "/login";
  const isNotFoundPage = location.pathname === "/404";

  useTokenValidator();

  return (
    <div className="min-h-screen bg-slate-50 flex flex-col">
      {!isLoginPage && !isNotFoundPage && (
        <SessionProgressBar
          key={`session-bar-${location.pathname}`}
          interval={30000}
        />
      )}

      {!isLoginPage && !isNotFoundPage && <Navbar />}

      <main className="flex-grow">
        <Routes>
          <Route path="/login" element={<Login />} />
          <Route element={<CheckAutentication />}>
            <Route path="/" element={<Navigate to="/dashboard" />} />
            <Route path="/dashboard" element={<Dashboard />} />
            <Route path="/lessons" element={<Lessons />} />
            <Route path="/students" element={<Students />} />
            <Route path="/teachers" element={<Teachers />} />
            <Route path="/authorization" element={<Authorization />}></Route>
            <Route path="*" element={<Navigate to="/404" replace />} />
          </Route>
          <Route path="/404" element={<NotFound />} />
        </Routes>
      </main>

      {!isLoginPage && !isNotFoundPage && <Footer />}
    </div>
  );
};

export default App;
