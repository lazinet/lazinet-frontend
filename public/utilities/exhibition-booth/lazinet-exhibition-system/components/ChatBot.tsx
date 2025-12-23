import React, { useState, useRef, useEffect } from 'react';
import { AppData } from '../types';
import { generateResponse } from '../services/geminiService';

interface ChatBotProps {
  data: AppData;
}

interface Message {
  role: 'user' | 'bot';
  text: string;
}

const ChatBot: React.FC<ChatBotProps> = ({ data }) => {
  const [isOpen, setIsOpen] = useState(false);
  const [messages, setMessages] = useState<Message[]>([
    { role: 'bot', text: `Welcome to ${data.organizer.title}! How can I help you today?` }
  ]);
  const [input, setInput] = useState('');
  const [isTyping, setIsTyping] = useState(false);
  const messagesEndRef = useRef<HTMLDivElement>(null);

  const scrollToBottom = () => {
    messagesEndRef.current?.scrollIntoView({ behavior: 'smooth' });
  };

  useEffect(() => {
    scrollToBottom();
  }, [messages, isOpen, isTyping]);

  const handleSend = async () => {
    if (!input.trim()) return;

    const userMsg = input;
    setMessages(prev => [...prev, { role: 'user', text: userMsg }]);
    setInput('');
    setIsTyping(true);

    const botResponse = await generateResponse(userMsg, data);
    
    setMessages(prev => [...prev, { role: 'bot', text: botResponse }]);
    setIsTyping(false);
  };

  return (
    <div className="fixed bottom-6 right-6 z-[60] flex flex-col items-end gap-4">
      {/* Chat Window */}
      <div 
        className={`
          w-[350px] h-[500px] bg-white rounded-[2.5rem] shadow-2xl flex flex-col overflow-hidden border border-slate-100 transition-all duration-300 origin-bottom-right
          ${isOpen ? 'scale-100 opacity-100' : 'scale-0 opacity-0 pointer-events-none'}
        `}
      >
        <div 
          className="p-6 flex items-center justify-between text-white"
          style={{ background: `linear-gradient(135deg, ${data.configs.themeColor1}, ${data.configs.themeColor2})` }}
        >
          <div className="flex items-center gap-3">
            <img src={data.configs.botLogo} alt="Bot" className="w-10 h-10 rounded-full border-2 border-white/30 bg-white object-cover" />
            <div>
              <p className="font-black text-sm tracking-tight">{data.configs.botName}</p>
              <p className="text-[9px] font-medium opacity-80 uppercase tracking-widest">Assistant Online</p>
            </div>
          </div>
          <button onClick={() => setIsOpen(false)} className="hover:bg-black/10 w-8 h-8 rounded-full transition flex items-center justify-center">
            <i className="fas fa-times"></i>
          </button>
        </div>

        <div className="flex-1 p-5 overflow-y-auto space-y-4 bg-slate-50/50 custom-scrollbar">
          {messages.map((m, idx) => (
            <div key={idx} className={`flex ${m.role === 'user' ? 'justify-end' : 'justify-start'}`}>
              <div 
                className={`
                  p-4 rounded-3xl text-sm shadow-sm max-w-[85%]
                  ${m.role === 'user' 
                    ? 'bg-blue-600 text-white rounded-tr-none' 
                    : 'bg-white border border-slate-100 text-slate-700 rounded-tl-none'}
                `}
              >
                {m.text}
              </div>
            </div>
          ))}
          
          {isTyping && (
             <div className="flex justify-start">
               <div className="bg-white border border-slate-100 px-4 py-3 rounded-3xl rounded-tl-none shadow-sm flex items-center gap-1">
                 <div className="typing-dot"></div>
                 <div className="typing-dot"></div>
                 <div className="typing-dot"></div>
               </div>
             </div>
          )}
          <div ref={messagesEndRef} />
        </div>

        <div className="p-4 bg-white border-t flex gap-2">
          <input 
            value={input}
            onChange={(e) => setInput(e.target.value)}
            onKeyDown={(e) => e.key === 'Enter' && handleSend()}
            type="text" 
            className="flex-1 bg-slate-100 border-none p-4 rounded-2xl text-sm focus:ring-2 focus:ring-blue-500 outline-none" 
            placeholder="Ask AI about booths..."
          />
          <button 
            onClick={handleSend}
            className="w-12 h-12 rounded-2xl text-white shadow-lg flex items-center justify-center transition active:scale-90"
            style={{ backgroundColor: data.configs.themeColor1 }}
          >
            <i className="fas fa-paper-plane"></i>
          </button>
        </div>
      </div>

      {/* Toggle Button */}
      <button 
        onClick={() => setIsOpen(!isOpen)}
        className="w-16 h-16 rounded-full shadow-2xl text-white text-2xl flex items-center justify-center hover:scale-110 transition active:scale-95"
        style={{ backgroundColor: data.configs.themeColor1 }}
      >
        <i className={`fas ${isOpen ? 'fa-times' : 'fa-comment-dots'}`}></i>
      </button>
    </div>
  );
};

export default ChatBot;