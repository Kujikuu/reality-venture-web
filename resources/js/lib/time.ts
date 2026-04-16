const pad = (n: number): string => (n < 10 ? `0${n}` : `${n}`);

const parseHHMM = (value: string): number | null => {
    const match = /^(\d{1,2}):(\d{2})/.exec(value);
    if (!match) {
        return null;
    }

    const hours = Number(match[1]);
    const minutes = Number(match[2]);

    if (Number.isNaN(hours) || Number.isNaN(minutes)) {
        return null;
    }

    return hours * 60 + minutes;
};

const formatHHMM = (totalMinutes: number): string => {
    const hours = Math.floor(totalMinutes / 60) % 24;
    const minutes = totalMinutes % 60;
    return `${pad(hours)}:${pad(minutes)}`;
};

export function generateTimeSlots(from: string, to: string, stepMinutes: number = 30): string[] {
    const start = parseHHMM(from);
    const end = parseHHMM(to);

    if (start === null || end === null || stepMinutes <= 0 || end <= start) {
        return [];
    }

    const slots: string[] = [];
    for (let t = start; t <= end; t += stepMinutes) {
        slots.push(formatHHMM(t));
    }
    return slots;
}

export function formatTimeLabel(value: string, format: '12h' | '24h' = '24h'): string {
    const total = parseHHMM(value);
    if (total === null) {
        return value;
    }

    const hours24 = Math.floor(total / 60) % 24;
    const minutes = total % 60;

    if (format === '24h') {
        return `${pad(hours24)}:${pad(minutes)}`;
    }

    const period = hours24 < 12 ? 'AM' : 'PM';
    const hours12 = hours24 % 12 === 0 ? 12 : hours24 % 12;
    return `${pad(hours12)}:${pad(minutes)} ${period}`;
}
