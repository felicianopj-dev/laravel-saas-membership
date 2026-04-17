export type AdminNavigationItem = {
    label: string
    href: string
    exact?: boolean
    startsWith?: string
}

export const adminNavigation: AdminNavigationItem[] = [
    {
        label: 'Dashboard',
        href: '/admin',
        exact: true,
    },
    {
        label: 'Users',
        href: '/admin/users',
        startsWith: '/admin/users',
    },
]