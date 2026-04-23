export type MemberNavigationItem = {
    label: string
    href: string
    exact?: boolean
    startsWith?: string
}

export const memberNavigation: MemberNavigationItem[] = [
    {
        label: 'Dashboard',
        href: '/member',
        exact: true,
    },
    {
        label: 'Profile',
        href: '/member/profile',
        startsWith: '/member/profile',
    },
]