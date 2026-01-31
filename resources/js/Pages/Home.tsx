import React from 'react';
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
