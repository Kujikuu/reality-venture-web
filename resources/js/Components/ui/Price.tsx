import { SarIcon } from './SarIcon';

interface PriceProps {
  amount: number | string;
  className?: string;
}

export function Price({ amount, className = '' }: PriceProps) {
  const formatted = typeof amount === 'number' ? Number(amount).toLocaleString() : amount;

  return (
    <span className={className}>
      <SarIcon /> {formatted}
    </span>
  );
}
