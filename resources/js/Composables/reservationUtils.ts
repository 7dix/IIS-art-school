// Composables/reservationUtils.ts
export const statusClassColor = (status: string) => {

    let color = () => {    
        switch (status) {
            case 'pending':
                return 'bg-yellow-500';
            case 'approved':
                return 'bg-green-500';
            case 'rejected':
                return 'bg-red-500';
            case 'ongoing':
                return 'bg-blue-500';
            case 'completed':
                return 'bg-green-900';
            default:
                return 'bg-gray-500';
        }}
    return `${color()} text-white hover:${color()} hover:text-white`;
}

export const parseDateTime = (date: string) => {
    const dateObj = new Date(date);
    return dateObj.toLocaleDateString('cs-CZ', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });
}