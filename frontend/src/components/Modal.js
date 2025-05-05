import React from "react";
import { X } from "lucide-react";

const Modal = ({ children, onClose }) => {
  let isMouseDownOutside = false;

  const handleOverlayMouseDown = (e) => {
    if (e.target === e.currentTarget) {
      isMouseDownOutside = true;
    } else {
      isMouseDownOutside = false;
    }
  };

  const handleOverlayMouseUp = (e) => {
    if (isMouseDownOutside && e.target === e.currentTarget) {
      onClose();
    }
  };

  return (
    <div
      className="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50"
      onMouseDown={handleOverlayMouseDown}
      onMouseUp={handleOverlayMouseUp}
    >
      <div className="bg-white rounded-lg shadow-lg p-6 w-full max-w-2xl relative overflow-y-auto max-h-[90vh]">
        <button
          className="absolute top-2 right-2 text-gray-500 hover:text-gray-700"
          onClick={onClose}
          aria-label="Fechar modal"
        >
          <X className="w-5 h-5" />
        </button>
        {children}
      </div>
    </div>
  );
};

export default Modal;
