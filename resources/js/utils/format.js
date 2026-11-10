// Small display helpers shared by the pages.

const priceFormat = new Intl.NumberFormat('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

/** 1250 or "1250.00" -> "Tk 1,250.00" (null/empty -> ""). */
export function formatPrice(value) {
    if (value === null || value === undefined || value === '') {
        return '';
    }

    return `Tk ${priceFormat.format(Number(value))}`;
}

/** Whole-number discount when the old price is above the price, else null. */
export function discountPercent(price, oldPrice) {
    const p = Number(price);
    const o = Number(oldPrice);

    if (!oldPrice || o <= p) {
        return null;
    }

    return Math.round((1 - p / o) * 100);
}
