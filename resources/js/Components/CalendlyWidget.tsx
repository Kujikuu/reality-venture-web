import { useEffect } from 'react';
import { InlineWidget } from 'react-calendly';

interface CalendlyWidgetProps {
  url: string;
  onBooked: (eventUuid: string) => void;
  prefillName?: string;
  prefillEmail?: string;
}

export default function CalendlyWidget({ url, onBooked, prefillName, prefillEmail }: CalendlyWidgetProps) {
  useEffect(() => {
    const handleMessage = (e: MessageEvent) => {
      if (e.data?.event === 'calendly.event_scheduled') {
        const eventUri = e.data?.payload?.event?.uri;
        if (eventUri) {
          const parts = eventUri.split('/');
          const eventUuid = parts[parts.length - 1];
          onBooked(eventUuid);
        }
      }
    };

    window.addEventListener('message', handleMessage);
    return () => window.removeEventListener('message', handleMessage);
  }, [onBooked]);

  return (
    <div className="calendly-widget-container">
      <InlineWidget
        url={url}
        styles={{ minWidth: '100%', height: '630px' }}
        prefill={{
          name: prefillName,
          email: prefillEmail,
        }}
      />
    </div>
  );
}
