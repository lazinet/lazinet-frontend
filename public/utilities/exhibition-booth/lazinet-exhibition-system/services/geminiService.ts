import { GoogleGenAI } from "@google/genai";
import { AppData } from '../types';

export const generateResponse = async (userMsg: string, appData: AppData): Promise<string> => {
  try {
    const apiKey = process.env.API_KEY;
    if (!apiKey) {
      return "Error: API Key is missing. Please check your environment variables.";
    }

    const availableBooths = appData.booths
      .filter(b => b.status === 'Available')
      .map(b => b.id)
      .join(', ');

    const scheduleText = appData.schedule
      .map(s => `[${s.date} ${s.start}-${s.end}: ${s.content}]`)
      .join('; ');

    const systemPrompt = `
      You are an intelligent assistant for the ${appData.organizer.title} exhibition.
      
      Here is the current real-time data:
      - Event Location: ${appData.organizer.location}, ${appData.organizer.address}.
      - Available Booths: ${availableBooths || "None"}.
      - Event Schedule: ${scheduleText}.
      
      Answer the user's question based on this data. Keep answers concise, helpful, and polite.
      If asked about booking, guide them to click on an available booth on the map.
    `;

    const ai = new GoogleGenAI({ apiKey });
    const response = await ai.models.generateContent({
      model: 'gemini-3-flash-preview',
      contents: userMsg,
      config: {
        systemInstruction: systemPrompt,
      }
    });

    return response.text || "I'm sorry, I couldn't generate a response.";
  } catch (error) {
    console.error("Gemini API Error:", error);
    return "I'm having trouble connecting to the AI service right now.";
  }
};