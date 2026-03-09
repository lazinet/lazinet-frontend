import React, { useState } from 'react';
import { AppData, Booth, ScheduleItem } from '../types';
import MapCanvas from './MapCanvas';

interface AdminPanelProps {
  data: AppData;
  onUpdate: (newData: AppData) => void;
  onLogout: () => void;
}

const AdminPanel: React.FC<AdminPanelProps> = ({ data, onUpdate, onLogout }) => {
  const [activeTab, setActiveTab] = useState<'details' | 'booths' | 'schedule'>('details');

  const updateOrganizer = (field: string, value: string | number) => {
    onUpdate({
      ...data,
      organizer: { ...data.organizer, [field]: value }
    });
  };

  const updateBooth = (index: number, field: keyof Booth, value: any) => {
    const newBooths = [...data.booths];
    newBooths[index] = { ...newBooths[index], [field]: value };
    onUpdate({ ...data, booths: newBooths });
  };

  const addBooth = () => {
    const newBooth: Booth = {
      id: "NEW", x: 50, y: 50, width: 100, height: 100, status: 'Available', rank: 'Gold', price: 0
    };
    onUpdate({ ...data, booths: [...data.booths, newBooth] });
  };

  const removeBooth = (index: number) => {
    const newBooths = data.booths.filter((_, i) => i !== index);
    onUpdate({ ...data, booths: newBooths });
  };

  return (
    <div className="flex h-screen bg-slate-50">
      {/* Sidebar */}
      <div className="w-64 bg-white border-r border-slate-200 flex flex-col">
        <div className="p-6 border-b border-slate-100">
          <h2 className="font-black text-xl text-slate-800 tracking-tighter">ADMIN HUB</h2>
          <p className="text-xs text-slate-400 font-bold uppercase tracking-widest">Management System</p>
        </div>
        <nav className="flex-1 p-4 space-y-2">
          {['details', 'booths', 'schedule'].map((tab) => (
            <button
              key={tab}
              onClick={() => setActiveTab(tab as any)}
              className={`w-full text-left px-4 py-3 rounded-xl text-sm font-bold capitalize transition-colors ${
                activeTab === tab ? 'bg-blue-50 text-blue-600' : 'text-slate-500 hover:bg-slate-50'
              }`}
            >
              {tab}
            </button>
          ))}
        </nav>
        <div className="p-4 border-t border-slate-100">
          <button onClick={onLogout} className="w-full bg-slate-900 text-white py-3 rounded-xl font-bold text-sm">Logout</button>
        </div>
      </div>

      {/* Main Content */}
      <div className="flex-1 flex flex-col h-full overflow-hidden">
        {/* Top Bar */}
        <div className="h-16 bg-white border-b border-slate-100 flex items-center justify-between px-6">
          <h3 className="font-bold text-slate-700 capitalize">{activeTab} Configuration</h3>
          <div className="flex gap-2">
             <span className="text-xs font-bold text-emerald-500 bg-emerald-50 px-3 py-1 rounded-full border border-emerald-100">
               Auto-Saving Locally
             </span>
          </div>
        </div>

        <div className="flex-1 p-6 overflow-hidden flex gap-6">
          {/* Content Area */}
          <div className="flex-1 bg-white rounded-[2rem] shadow-sm border border-slate-100 overflow-hidden flex flex-col">
             
             {activeTab === 'details' && (
               <div className="p-8 space-y-6 overflow-y-auto">
                 <div className="grid grid-cols-2 gap-6">
                   <div>
                     <label className="text-xs font-bold text-slate-400 uppercase">Event Title</label>
                     <input 
                       value={data.organizer.title} 
                       onChange={(e) => updateOrganizer('title', e.target.value)}
                       className="w-full border-b border-slate-200 py-2 font-bold text-slate-700 focus:border-blue-500 outline-none" 
                     />
                   </div>
                   <div>
                     <label className="text-xs font-bold text-slate-400 uppercase">Location</label>
                     <input 
                       value={data.organizer.location} 
                       onChange={(e) => updateOrganizer('location', e.target.value)}
                       className="w-full border-b border-slate-200 py-2 font-bold text-slate-700 focus:border-blue-500 outline-none" 
                     />
                   </div>
                   <div>
                     <label className="text-xs font-bold text-slate-400 uppercase">Map Width (px)</label>
                     <input type="number"
                       value={data.organizer.width} 
                       onChange={(e) => updateOrganizer('width', Number(e.target.value))}
                       className="w-full border-b border-slate-200 py-2 font-bold text-slate-700 focus:border-blue-500 outline-none" 
                     />
                   </div>
                   <div>
                     <label className="text-xs font-bold text-slate-400 uppercase">Map Height (px)</label>
                     <input type="number"
                       value={data.organizer.height} 
                       onChange={(e) => updateOrganizer('height', Number(e.target.value))}
                       className="w-full border-b border-slate-200 py-2 font-bold text-slate-700 focus:border-blue-500 outline-none" 
                     />
                   </div>
                 </div>
               </div>
             )}

             {activeTab === 'booths' && (
               <div className="flex-1 flex flex-col">
                 <div className="p-4 border-b border-slate-100 flex justify-between">
                   <h4 className="font-bold text-sm">Booth List</h4>
                   <button onClick={addBooth} className="bg-blue-600 text-white px-4 py-1.5 rounded-lg text-xs font-bold hover:bg-blue-700">+ Add Booth</button>
                 </div>
                 <div className="flex-1 overflow-auto custom-scrollbar p-0">
                   <table className="w-full text-left">
                     <thead className="bg-slate-50 sticky top-0 z-10">
                       <tr>
                         <th className="p-3 text-xs font-black text-slate-400 uppercase">ID</th>
                         <th className="p-3 text-xs font-black text-slate-400 uppercase">X</th>
                         <th className="p-3 text-xs font-black text-slate-400 uppercase">Y</th>
                         <th className="p-3 text-xs font-black text-slate-400 uppercase">W</th>
                         <th className="p-3 text-xs font-black text-slate-400 uppercase">H</th>
                         <th className="p-3 text-xs font-black text-slate-400 uppercase">Status</th>
                         <th className="p-3 text-xs font-black text-slate-400 uppercase">Actions</th>
                       </tr>
                     </thead>
                     <tbody className="divide-y divide-slate-100">
                       {data.booths.map((b, idx) => (
                         <tr key={idx} className="hover:bg-slate-50">
                           <td className="p-3"><input value={b.id} onChange={(e) => updateBooth(idx, 'id', e.target.value)} className="w-16 bg-transparent font-bold outline-none"/></td>
                           <td className="p-3"><input type="number" value={b.x} onChange={(e) => updateBooth(idx, 'x', Number(e.target.value))} className="w-12 bg-transparent text-sm outline-none"/></td>
                           <td className="p-3"><input type="number" value={b.y} onChange={(e) => updateBooth(idx, 'y', Number(e.target.value))} className="w-12 bg-transparent text-sm outline-none"/></td>
                           <td className="p-3"><input type="number" value={b.width} onChange={(e) => updateBooth(idx, 'width', Number(e.target.value))} className="w-12 bg-transparent text-sm outline-none"/></td>
                           <td className="p-3"><input type="number" value={b.height} onChange={(e) => updateBooth(idx, 'height', Number(e.target.value))} className="w-12 bg-transparent text-sm outline-none"/></td>
                           <td className="p-3">
                             <select value={b.status} onChange={(e) => updateBooth(idx, 'status', e.target.value)} className="bg-transparent text-xs font-bold outline-none">
                               <option value="Available">Available</option>
                               <option value="Booked">Booked</option>
                               <option value="Occupied">Occupied</option>
                             </select>
                           </td>
                           <td className="p-3">
                             <button onClick={() => removeBooth(idx)} className="text-red-400 hover:text-red-600"><i className="fas fa-trash"></i></button>
                           </td>
                         </tr>
                       ))}
                     </tbody>
                   </table>
                 </div>
               </div>
             )}
             
             {activeTab === 'schedule' && (
               <div className="p-8">
                 <p className="text-slate-400 italic">Schedule editing is simplified for this demo.</p>
                 <div className="mt-4 space-y-2">
                   {data.schedule.map((s) => (
                     <div key={s.id} className="p-4 bg-slate-50 rounded-xl border border-slate-200">
                       <span className="font-bold text-blue-600 mr-2">[{s.date}]</span>
                       <span className="font-bold">{s.start} - {s.end}:</span> {s.content}
                     </div>
                   ))}
                 </div>
               </div>
             )}
          </div>

          {/* Live Preview Panel */}
          <div className="w-[400px] bg-slate-200 rounded-[2rem] border-4 border-white shadow-inner flex flex-col overflow-hidden relative">
             <div className="absolute top-4 left-4 z-10 bg-white/80 backdrop-blur px-3 py-1 rounded-full text-[10px] font-black uppercase text-slate-500 shadow-sm">Live Preview</div>
             <div className="flex-1">
               <MapCanvas data={data} isAdmin={true} />
             </div>
          </div>
        </div>
      </div>
    </div>
  );
};

export default AdminPanel;