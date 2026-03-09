import { AppData } from './types';

export const INITIAL_DATA: AppData = {
  configs: {
    botName: "Lazinet Bot",
    botLogo: "https://picsum.photos/100/100",
    themeColor1: "#2563eb",
    themeColor2: "#1e40af",
    waitingMsg: "AI is thinking..."
  },
  organizer: {
    shortName: "LAZINET",
    fullName: "Lazinet Exhibition Hub",
    logo: "https://picsum.photos/200/200",
    title: "TECH EXPO 2024",
    location: "SECC District 7",
    address: "799 Nguyen Van Linh, HCMC",
    width: 2000,
    height: 1500,
    ratio: 1
  },
  schedule: [
    { id: 1, date: "20/10/2024", start: "08:00", end: "09:00", content: "Opening Ceremony" },
    { id: 2, date: "20/10/2024", start: "09:00", end: "12:00", content: "B2B Networking Session" },
    { id: 3, date: "21/10/2024", start: "14:00", end: "16:00", content: "Startup Pitching" }
  ],
  booths: [
    { id: "A01", x: 100, y: 100, width: 200, height: 200, status: "Occupied", rank: "Diamond", price: 50000000 },
    { id: "A02", x: 350, y: 100, width: 200, height: 200, status: "Available", rank: "Diamond", price: 50000000 },
    { id: "B01", x: 100, y: 400, width: 150, height: 150, status: "Booked", rank: "Gold", price: 20000000, companyName: "Tech Corp" },
    { id: "B02", x: 300, y: 400, width: 150, height: 150, status: "Available", rank: "Gold", price: 20000000 },
    { id: "B03", x: 500, y: 400, width: 150, height: 150, status: "Available", rank: "Gold", price: 20000000 },
    { id: "C01", x: 100, y: 650, width: 100, height: 100, status: "Available", rank: "Silver", price: 10000000 },
    { id: "C02", x: 250, y: 650, width: 100, height: 100, status: "Available", rank: "Silver", price: 10000000 },
    { id: "C03", x: 400, y: 650, width: 100, height: 100, status: "Available", rank: "Silver", price: 10000000 }
  ]
};

export const ADMIN_PASS = "admin123"; // Simple simulation