import React, { useEffect } from 'react';
import { useTranslation } from 'react-i18next';
import { Hero } from '../Components/Hero';
import { VisionMission } from '../Components/VisionMission';
import { StrategicGoals } from '../Components/StrategicGoals';
import { TargetAudience } from '../Components/TargetAudience';
import { Services } from '../Components/Services';
import { Ventures } from '../Components/Ventures';
import { Programs } from '../Components/Programs';
import { Process } from '../Components/Process';
import { Team } from '../Components/Team';
import { LatestPosts } from '../Components/LatestPosts';
import { NewsletterSubscribe } from '../Components/NewsletterSubscribe';
import { SEO } from '../Components/SEO';
import { AdBanner } from '../Components/AdBanner';

export default function Home() {
  const { t } = useTranslation('common');

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
      <SEO />
      <AdBanner position="top" />
      <Hero />
      <VisionMission />
      <StrategicGoals />
      <TargetAudience />
      <Services />
      <AdBanner position="middle" />
      {/* <Ventures /> */}
      <Programs />
      <Team />
      <NewsletterSubscribe
        sectionId="rv-club"
        heading={t('newsletter.home.heading')}
        description={t('newsletter.home.description')}
        badge={t('newsletter.home.badge')}
      />
      {/* <Process /> */}
      <LatestPosts />
    </>
  );
}
