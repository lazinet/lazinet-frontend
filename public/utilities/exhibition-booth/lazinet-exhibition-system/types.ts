export interface Booth {
  id: string;
  x: number;
  y: number;
  width: number;
  height: number;
  radius?: number;
  status: 'Available' | 'Booked' | 'Occupied';
  rank: 'Diamond' | 'Platinum' | 'Gold' | 'Silver';
  price: number;
  qrLink?: string;
  customerName?: string;
  companyName?: string;
}

export interface ScheduleItem {
  id: number;
  date: string;
  start: string;
  end: string;
  content: string;
}

export interface OrganizerConfig {
  shortName: string;
  fullName: string;
  logo: string;
  title: string;
  location: string;
  address: string;
  width: number;
  height: number;
  ratio: number;
}

export interface AppConfigs {
  botName: string;
  botLogo: string;
  themeColor1: string;
  themeColor2: string;
  waitingMsg: string;
}

export interface AppData {
  organizer: OrganizerConfig;
  booths: Booth[];
  schedule: ScheduleItem[];
  configs: AppConfigs;
}