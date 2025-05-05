import { useEffect, useState, useRef } from "react";

const SessionProgressBar = ({ interval = 30000 }) => {
  const [progress, setProgress] = useState(0);
  const progressRef = useRef(0);
  const stepTime = 100;

  useEffect(() => {
    let startTime = Date.now();

    const intervalId = setInterval(() => {
      const elapsed = Date.now() - startTime;
      const percentage = Math.min((elapsed / interval) * 100, 100);
      setProgress(percentage);
      progressRef.current = percentage;

      if (percentage >= 100) {
        startTime = Date.now();
        setProgress(0);
        progressRef.current = 0;
      }
    }, stepTime);

    return () => clearInterval(intervalId);
  }, [interval]);

  return (
    <div
      className="fixed top-0 left-0 w-full h-0.5 bg-transparent pointer-events-none"
      style={{ zIndex: 9999999999 }}
    >
      <div
        className="h-full bg-[#FB923C] transition-all duration-75 ease-linear"
        style={{ width: `${progress}%` }}
      ></div>
    </div>
  );
};

export default SessionProgressBar;
