import './globals.scss'

export const metadata = {
  title: 'BEST TODO APP',
  description: 'TODO APP',
}

export default function RootLayout({ children }) {
  return (
    <html lang="fr">
      <body>{children}</body>
    </html>
  )
}
