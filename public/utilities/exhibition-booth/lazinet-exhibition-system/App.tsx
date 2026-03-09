import React, { useState } from 'react';
import { INITIAL_DATA, ADMIN_PASS } from './constants';
import { AppData, Booth } from './types';
import MapCanvas from './components/MapCanvas';
import ChatBot from './components/ChatBot';
import AdminPanel from './components/AdminPanel';

const App: React.FC = () => {
  const [data, setData] = useState<AppData>(INITIAL_DATA);
  const [view, setView] = useState<'home' | 'admin' | 'login'>('home');
  const [password, setPassword] = useState('');
  const [bookingModal, setBookingModal] = useState<Booth | null>(null);

  // Handlers
  const handleLogin = () => {
    if (password === ADMIN_PASS) {
      setView('admin');
      setPassword('');
    } else {
      alert("Invalid Password!");
    }
  };

  const handleBoothClick = (booth: Booth) => {
    if (booth.status !== 'Available') {
      alert(`Booth ${booth.id} is ${booth.status}.`);
      return;
    }
    setBookingModal(booth);
  };

  const confirmBooking = () => {
    if (!bookingModal) return;
    const updatedBooths = data.booths.map(b => 
      b.id === bookingModal.id ? { ...b, status: 'Booked' as const } : b
    );
    setData({ ...data, booths: updatedBooths });
    setBookingModal(null);
    alert("Booking request submitted! Please proceed to payment.");
  };

  // --- Render Views ---

  if (view === 'admin') {
    return (
      <AdminPanel 
        data={data} 
        onUpdate={setData} 
        onLogout={() => setView('home')} 
      />
    );
  }

  if (view === 'login') {
    return (
      <div className="min-h-screen bg-slate-900 flex items-center justify-center p-4">
        <div className="bg-white w-full max-w-md rounded-[2.5rem] p-10 shadow-2xl text-center">
          <div className="w-20 h-20 bg-blue-600 rounded-3xl mx-auto mb-6 flex items-center justify-center shadow-lg shadow-blue-200">
            <i className="fas fa-user-shield text-white text-4xl"></i>
          </div>
          <h2 className="text-2xl font-black text-slate-800 mb-2 tracking-tighter uppercase">Admin Hub Access</h2>
          <input 
            type="password" 
            value={password}
            onChange={(e) => setPassword(e.target.value)}
            className="w-full bg-slate-50 border-none p-5 rounded-2xl mb-6 focus:ring-2 focus:ring-blue-500 outline-none text-center font-bold tracking-widest text-lg" 
            placeholder="••••••••"
          />
          <button onClick={handleLogin} className="w-full bg-slate-900 hover:bg-blue-600 text-white font-bold py-5 rounded-2xl transition-all active:scale-95 shadow-xl">
            VERIFY ACCESS
          </button>
          <button onClick={() => setView('home')} className="mt-4 text-slate-400 text-xs font-bold hover:text-slate-600">
            Return to Visitor View
          </button>
        </div>
      </div>
    );
  }

  // --- Visitor View (Home) ---
  return (
    <div className="min-h-screen flex flex-col relative">
      {/* Navbar */}
      <nav className="h-20 px-6 md:px-10 flex items-center justify-between sticky top-0 z-50 bg-white/90 backdrop-blur border-b border-slate-100">
        <div className="flex items-center gap-4">
          <img src={data.organizer.logo} alt="Logo" className="h-10 w-10 object-contain rounded-xl bg-slate-50 p-1 border" />
          <div className="leading-tight">
            <h1 className="font-black text-slate-900 text-lg md:text-xl tracking-tighter uppercase italic">{data.organizer.title}</h1>
            <p className="text-[10px] font-bold text-blue-600 tracking-widest">{data.organizer.location}</p>
          </div>
        </div>
        <div className="flex items-center gap-6">
          <div className="hidden lg:flex gap-5 text-[10px] font-black uppercase tracking-widest text-slate-400">
            <span className="flex items-center gap-2"><i className="w-3 h-3 bg-white border border-slate-300 rounded-sm"></i> Available</span>
            <span className="flex items-center gap-2"><i className="w-3 h-3 bg-yellow-400 rounded-sm"></i> Booked</span>
            <span className="flex items-center gap-2"><i className="w-3 h-3 bg-emerald-500 rounded-sm"></i> Occupied</span>
          </div>
          <button onClick={() => setView('login')} className="text-slate-400 hover:text-slate-600">
            <i className="fas fa-cog"></i>
          </button>
        </div>
      </nav>

      {/* Main Map Viewport */}
      <div className="flex-1 w-full h-[calc(100vh-80px)]">
        <MapCanvas data={data} onBoothClick={handleBoothClick} />
      </div>

      {/* Booking Modal */}
      {bookingModal && (
        <div className="fixed inset-0 z-[70] flex items-center justify-center bg-black/40 backdrop-blur-sm p-4">
          <div className="bg-white w-full max-w-sm rounded-[2rem] p-6 shadow-2xl animate-in fade-in zoom-in duration-200">
            <h3 className="text-xl font-black text-slate-800 mb-4 uppercase italic">Booking Booth {bookingModal.id}</h3>
            <div className="bg-blue-50 p-4 rounded-2xl flex items-center gap-4 mb-4">
               <div>
                  <p className="text-[10px] font-black text-blue-600 uppercase tracking-widest">{bookingModal.rank}</p>
                  <p className="text-xl font-black text-slate-800">{bookingModal.price.toLocaleString()} VND</p>
               </div>
            </div>
            <div className="space-y-3 mb-6">
              <input className="w-full bg-slate-50 p-3 rounded-xl text-sm outline-none border border-transparent focus:border-blue-500" placeholder="Company Name *" />
              <input className="w-full bg-slate-50 p-3 rounded-xl text-sm outline-none border border-transparent focus:border-blue-500" placeholder="Contact Name *" />
              <input className="w-full bg-slate-50 p-3 rounded-xl text-sm outline-none border border-transparent focus:border-blue-500" placeholder="Phone Number *" />
            </div>
            <div className="flex gap-3">
              <button onClick={() => setBookingModal(null)} className="flex-1 py-3 font-bold text-slate-500 hover:bg-slate-100 rounded-xl">Cancel</button>
              <button onClick={confirmBooking} className="flex-1 py-3 font-bold text-white bg-blue-600 hover:bg-blue-700 rounded-xl shadow-lg shadow-blue-200">Confirm</button>
            </div>
          </div>
        </div>
      )}

      {/* Chat Bot */}
      <ChatBot data={data} />
    </div>
  );
};

export default App;