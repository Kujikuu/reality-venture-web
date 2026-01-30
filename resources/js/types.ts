import React from 'react';

export interface ServiceItem {
  icon: React.ElementType;
  title?: string;
  description?: string;
  titleKey?: string;
  descriptionKey?: string;
}

export interface ProgramItem {
  title: string;
  description: string;
  audience: string;
  details?: string[];
  cta: string;
}

export interface TeamMember {
  name: string;
  role: string;
  image?: string;
  bio?: string;
}

export interface ProcessStep {
  number: string;
  title: string;
  description: string;
}

export interface TargetAudienceItem {
  title: string;
  description: string;
}