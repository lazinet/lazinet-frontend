import React, { useRef, useEffect, useState } from 'react';
import { AppData, Booth } from '../types';

interface MapCanvasProps {
  data: AppData;
  onBoothClick?: (booth: Booth) => void;
  isAdmin?: boolean;
}

const MapCanvas: React.FC<MapCanvasProps> = ({ data, onBoothClick, isAdmin = false }) => {
  const canvasRef = useRef<HTMLCanvasElement>(null);
  const containerRef = useRef<HTMLDivElement>(null);
  
  // Camera State
  const [camera, setCamera] = useState({ x: 0, y: 0, zoom: 1 });
  const isDragging = useRef(false);
  const lastPos = useRef({ x: 0, y: 0 });

  // Reset Camera Logic
  const resetCamera = () => {
    if (!containerRef.current) return;
    const { width: cw, height: ch } = containerRef.current.getBoundingClientRect();
    const mapW = data.organizer.width * data.organizer.ratio;
    const mapH = data.organizer.height * data.organizer.ratio;
    
    const newZoom = Math.min(cw / mapW, ch / mapH) * 0.8;
    setCamera({
      zoom: newZoom,
      x: (cw - mapW * newZoom) / 2,
      y: (ch - mapH * newZoom) / 2
    });
  };

  useEffect(() => {
    resetCamera();
    window.addEventListener('resize', resetCamera);
    return () => window.removeEventListener('resize', resetCamera);
  // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [data.organizer]);

  // Drawing Logic
  useEffect(() => {
    const canvas = canvasRef.current;
    const ctx = canvas?.getContext('2d');
    const container = containerRef.current;
    
    if (!canvas || !ctx || !container) return;

    // Set canvas resolution to match display size
    canvas.width = container.clientWidth;
    canvas.height = container.clientHeight;

    const render = () => {
      // Clear
      ctx.clearRect(0, 0, canvas.width, canvas.height);
      ctx.save();
      
      // Apply Camera
      ctx.translate(camera.x, camera.y);
      ctx.scale(camera.zoom, camera.zoom);

      const r = data.organizer.ratio;
      
      // Draw Map Background
      ctx.fillStyle = 'white';
      ctx.shadowColor = 'rgba(0,0,0,0.1)';
      ctx.shadowBlur = 30 / camera.zoom;
      ctx.fillRect(0, 0, data.organizer.width * r, data.organizer.height * r);
      ctx.shadowBlur = 0;

      // Draw Grid (Optional, nice for admin)
      if (isAdmin) {
        ctx.strokeStyle = '#f1f5f9';
        ctx.lineWidth = 1 / camera.zoom;
        ctx.beginPath();
        for(let x=0; x<=data.organizer.width*r; x+=50*r) { ctx.moveTo(x,0); ctx.lineTo(x, data.organizer.height*r); }
        for(let y=0; y<=data.organizer.height*r; y+=50*r) { ctx.moveTo(0,y); ctx.lineTo(data.organizer.width*r, y); }
        ctx.stroke();
      }

      // Draw Booths
      data.booths.forEach(b => {
        const bx = b.x * r;
        const by = b.y * r;
        const bw = b.width * r;
        const bh = b.height * r;
        const radius = (b.radius || 0) * r;

        ctx.beginPath();
        if (ctx.roundRect) {
            ctx.roundRect(bx, by, bw, bh, radius);
        } else {
            ctx.rect(bx, by, bw, bh);
        }

        // Color based on status
        if (b.status === 'Occupied') ctx.fillStyle = '#10b981'; // Emerald 500
        else if (b.status === 'Booked') ctx.fillStyle = '#fbbf24'; // Amber 400
        else ctx.fillStyle = '#ffffff'; // White

        ctx.fill();
        ctx.strokeStyle = isAdmin ? '#94a3b8' : '#e2e8f0';
        ctx.lineWidth = (isAdmin ? 2 : 1) / camera.zoom;
        ctx.stroke();

        // Text
        ctx.fillStyle = b.status === 'Available' ? '#94a3b8' : '#ffffff';
        ctx.font = `bold ${Math.max(10, 10 / camera.zoom)}px Inter`;
        ctx.fillText(b.id, bx + 5, by + 15);
      });

      ctx.restore();
    };

    const animationId = requestAnimationFrame(render);
    return () => cancelAnimationFrame(animationId);
  }, [camera, data, isAdmin]);

  // Event Handlers
  const handleMouseDown = (e: React.MouseEvent | React.TouchEvent) => {
    isDragging.current = true;
    const clientX = 'touches' in e ? e.touches[0].clientX : e.clientX;
    const clientY = 'touches' in e ? e.touches[0].clientY : e.clientY;
    lastPos.current = { x: clientX, y: clientY };
  };

  const handleMouseMove = (e: React.MouseEvent | React.TouchEvent) => {
    if (!isDragging.current) return;
    const clientX = 'touches' in e ? e.touches[0].clientX : e.clientX;
    const clientY = 'touches' in e ? e.touches[0].clientY : e.clientY;

    const dx = clientX - lastPos.current.x;
    const dy = clientY - lastPos.current.y;
    
    setCamera(prev => ({ ...prev, x: prev.x + dx, y: prev.y + dy }));
    lastPos.current = { x: clientX, y: clientY };
  };

  const handleMouseUp = () => {
    isDragging.current = false;
  };

  const handleWheel = (e: React.WheelEvent) => {
    const delta = e.deltaY > 0 ? 0.9 : 1.1;
    const rect = containerRef.current!.getBoundingClientRect();
    const mouseX = e.clientX - rect.left;
    const mouseY = e.clientY - rect.top;

    setCamera(prev => ({
      ...prev,
      zoom: prev.zoom * delta,
      x: mouseX - (mouseX - prev.x) * delta,
      y: mouseY - (mouseY - prev.y) * delta
    }));
  };

  const handleClick = (e: React.MouseEvent) => {
    if (isDragging.current) return;
    if (!onBoothClick) return;

    const rect = containerRef.current!.getBoundingClientRect();
    const mouseX = e.clientX - rect.left;
    const mouseY = e.clientY - rect.top;

    // Convert screen coordinates to map coordinates
    const mapX = (mouseX - camera.x) / camera.zoom;
    const mapY = (mouseY - camera.y) / camera.zoom;
    const r = data.organizer.ratio;

    const booth = data.booths.find(b => {
       const bx = b.x * r;
       const by = b.y * r;
       const bw = b.width * r;
       const bh = b.height * r;
       return mapX >= bx && mapX <= bx + bw && mapY >= by && mapY <= by + bh;
    });

    if (booth) onBoothClick(booth);
  };

  return (
    <div ref={containerRef} className="w-full h-full relative bg-slate-100 overflow-hidden cursor-move touch-none">
      <canvas
        ref={canvasRef}
        onMouseDown={handleMouseDown}
        onMouseMove={handleMouseMove}
        onMouseUp={handleMouseUp}
        onMouseLeave={handleMouseUp}
        onTouchStart={handleMouseDown}
        onTouchMove={handleMouseMove}
        onTouchEnd={handleMouseUp}
        onWheel={handleWheel}
        onClick={handleClick}
        className="w-full h-full block"
      />
      {!isAdmin && (
        <button onClick={resetCamera} className="absolute top-4 right-4 bg-slate-900 text-white px-4 py-2 rounded-xl text-xs font-bold shadow-lg uppercase">
          Reset View
        </button>
      )}
    </div>
  );
};

export default MapCanvas;