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

export default function Home() {
  return (
    <>
      <Hero />
      <VisionMission />
      <StrategicGoals />
      <TargetAudience />
      <Services />
      <Ventures />
      <Programs />
      <Process />
      {/* <Team /> */}
    </>
  );
}
