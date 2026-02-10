import React, { useEffect } from 'react';
import { Hero } from '../Components/Hero';
import { VisionMission } from '../Components/VisionMission';
import { StrategicGoals } from '../Components/StrategicGoals';
import { TargetAudience } from '../Components/TargetAudience';
import { Services } from '../Components/Services';
import { Ventures } from '../Components/Ventures';
import { Programs } from '../Components/Programs';
import { Process } from '../Components/Process';
import { Team } from '../Components/Team';
import { Head } from '@inertiajs/react';
import { AdBanner } from '../Components/AdBanner';

export default function Home() {
  useEffect(() => {
    const hash = window.location.hash.replace('#', '');
    if (!hash) {
      return;
    }

    const scrollToHash = () => {
      const element = document.getElementById(hash);
      if (element) {
        element.scrollIntoView({ behavior: 'smooth' });
      }
    };

    const timeoutId = window.setTimeout(scrollToHash, 0);
    return () => window.clearTimeout(timeoutId);
  }, []);

  return (
    <>
      <AdBanner position="top" />
      <Hero />
      <VisionMission />
      <StrategicGoals />
      <TargetAudience />
      <Services />
      <AdBanner position="middle" />
      <Ventures />
      <Programs />
      <Process />
      <Team />
    </>
  );
}
