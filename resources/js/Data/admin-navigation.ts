export type NavigationItem = {
    label: string
    href: string
}

export const adminNavigation: NavigationItem[] = [
    {
        label: 'Dashboard',
        href: '/admin',
    },
    {
        label: 'Users',
        href: '/admin/users',
    },
]