import {
    LayoutDashboard,
    Users,
    UserPlus,
    UsersRound,
    Wrench,
    CirclePlus,
    Settings,
    FileText,
    FilePlus,
    Boxes,
    ArrowLeftRight,
    Tag,
    CreditCard,
    BarChart3,
    ClipboardList,
    ScrollText,
    Trash2,
} from 'lucide-react';

export const NAV_SECTIONS = [
    {
        items: [{ label: 'Dashboard', to: '/dashboard', icon: LayoutDashboard }],
    },
    {
        section: 'Customers',
        items: [
            { label: 'All Customers', to: '/customers', icon: Users },
            { label: 'Create Customer', to: '/customers/create', icon: UserPlus },
        ],
    },
    {
        section: 'Repairs',
        items: [
            { label: 'All Repairs', to: '/repairs', icon: Wrench },
            { label: 'Create Repair', to: '/repairs/create', icon: CirclePlus },
            { label: 'Settings', to: '/repairs/settings', icon: Settings },
        ],
    },
    {
        section: 'Invoices',
        items: [
            { label: 'All Invoices', to: '/invoices', icon: FileText },
            { label: 'Create Invoice', to: '/invoices/create', icon: FilePlus },
            { label: 'Settings', to: '/invoices/settings', icon: Settings },
        ],
    },
    {
        section: 'Inventory',
        items: [
            { label: 'Products', to: '/inventory/products', icon: Boxes },
            { label: 'Transactions', to: '/inventory/transactions', icon: ArrowLeftRight },
            { label: 'Categories', to: '/inventory/categories', icon: Tag },
        ],
    },
    {
        section: 'Management',
        adminOnly: true,
        items: [
            { label: 'Payments', to: '/payments', icon: CreditCard },
            { label: 'Create Report', to: '/reports', icon: BarChart3 },
            { label: 'Register Report', to: '/reports/register', icon: ClipboardList },
            { label: 'Users', to: '/users', icon: UsersRound },
            { label: 'Create User', to: '/register', icon: UserPlus },
            { label: 'Settings', to: '/settings', icon: Settings },
            { label: 'Activity Log', to: '/logs', icon: ScrollText },
            { label: 'Trash', to: '/trash', icon: Trash2 },
        ],
    },
];