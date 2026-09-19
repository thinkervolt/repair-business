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
        items: [{ label: 'nav.dashboard', to: '/dashboard', icon: LayoutDashboard }],
    },
    {
        section: 'nav.customers',
        items: [
            { label: 'nav.all_customers', to: '/customers', icon: Users },
            { label: 'nav.create_customer', to: '/customers/create', icon: UserPlus },
        ],
    },
    {
        section: 'nav.repairs',
        items: [
            { label: 'nav.all_repairs', to: '/repairs', icon: Wrench },
            { label: 'nav.create_repair', to: '/repairs/create', icon: CirclePlus },
            { label: 'nav.settings', to: '/repairs/settings', icon: Settings },
        ],
    },
    {
        section: 'nav.invoices',
        items: [
            { label: 'nav.all_invoices', to: '/invoices', icon: FileText },
            { label: 'nav.create_invoice', to: '/invoices/create', icon: FilePlus },
            { label: 'nav.settings', to: '/invoices/settings', icon: Settings },
        ],
    },
    {
        section: 'nav.inventory',
        items: [
            { label: 'nav.products', to: '/inventory/products', icon: Boxes },
            { label: 'nav.transactions', to: '/inventory/transactions', icon: ArrowLeftRight },
            { label: 'nav.categories', to: '/inventory/categories', icon: Tag },
        ],
    },
    {
        section: 'nav.management',
        adminOnly: true,
        items: [
            { label: 'nav.payments', to: '/payments', icon: CreditCard },
            { label: 'nav.create_report', to: '/reports', icon: BarChart3 },
            { label: 'nav.register_report', to: '/reports/register', icon: ClipboardList },
            { label: 'nav.users', to: '/users', icon: UsersRound },
            { label: 'nav.settings', to: '/settings', icon: Settings },
            { label: 'nav.activity_log', to: '/logs', icon: ScrollText },
            { label: 'nav.trash', to: '/trash', icon: Trash2 },
        ],
    },
];