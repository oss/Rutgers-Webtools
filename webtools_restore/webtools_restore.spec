%define name webtools_restore
%define version 2.0
%define release 6.ru6
%define prefix /usr/local
%define  debug_package %{nil}
%define _libdir %{prefix}/lib64

Summary: Restoremail webtool for Rutgers
Name: %name
Version: %version
Release: %release
License: GPL
Group: Services
Source0: http://jla.rutgers.edu/~brylon/Wget/webtools/linux/%{name}-%{version}.tgz
BuildRoot: %{_tmppath}/%{name}-root
Requires: maildrop sos-utils
Requires: webtools=2.0 
%description
Restoremail webtool for Rutgers

%prep
%setup -q

%build

%install

PATH="/usr/local/gnu/bin:$PATH"
export PATH

rm -rf $RPM_BUILD_ROOT
mkdir -p 0755 -p $RPM_BUILD_ROOT/usr/share/doc/%{name}-%{version}
mkdir -p 0755 $RPM_BUILD_ROOT%{_libdir}/webtools/config
mkdir -p 0755 $RPM_BUILD_ROOT%{_libdir}/webtools/html/restore
mkdir -p 0755 $RPM_BUILD_ROOT%{_libdir}/webtools/webbin

install -c -m 0644 $RPM_BUILD_DIR/%{name}-%{version}/README $RPM_BUILD_ROOT/usr/share/doc/%{name}-%{version}/
install -c -m 0644 $RPM_BUILD_DIR/%{name}-%{version}/Changelog $RPM_BUILD_ROOT/usr/share/doc/%{name}-%{version}/
install -c -m 0644 $RPM_BUILD_DIR/%{name}-%{version}/Todo $RPM_BUILD_ROOT/usr/share/doc/%{name}-%{version}/

install -c -m 0644 $RPM_BUILD_DIR/%{name}-%{version}/config/* $RPM_BUILD_ROOT%{_libdir}/webtools/config/
find . 
install -c -D -m 0644 $RPM_BUILD_DIR/%{name}-%{version}/html/restore/* $RPM_BUILD_ROOT%{_libdir}/webtools/html/restore
install -c -D -m 0644 $RPM_BUILD_DIR/%{name}-%{version}/webbin/* $RPM_BUILD_ROOT%{_libdir}/webtools/webbin

%post
echo "README is located at /usr/share/doc/%{name}-%{version}/README";
ln -s %{_libdir}/webtools/webbin/restore %{_libdir}/webtools/webbin/restoremail
ln -s %{_libdir}/webtools/html/restore %{_libdir}/webtools/html/restoremail
chmod 555 %{_libdir}/webtools/webbin/restore

%files
   %{_libdir}/webtools/config/restore_config.php.sample
   %{_libdir}/webtools/config/restoremail_config.php.sample
   %{_libdir}/webtools/html/restore/index.php
   %{_libdir}/webtools/html/restore/my.js
   %{_libdir}/webtools/html/restore/myfunctions.php
   %{_libdir}/webtools/html/restore/restore.php
   %{_libdir}/webtools/html/restore/showall.php
   %{_libdir}/webtools/html/restore/showsearch.php
   %{_libdir}/webtools/webbin/restore
   %{_docdir}/webtools_restore-2.0/README
   %{_docdir}/webtools_restore-2.0/Changelog
   %{_docdir}/webtools_restore-2.0/Todo



%changelog
* Mon Jan 09 2012 Josh Matthews <jam761@nbcs.rutgers.edu> - 2.0-6.ru6
- rebuilt for centos 6

* Tue Nov 29 2011 Phillip Quiza <pquiza@nbcs.rutgers.edu> - 2.0-6.ru
- updated source

* Fri Oct 28 2011 Phillip Quiza <pquiza@nbcs.rutgers.edu> - 2.0-5.ru
- changed %{_libdir} from /usr/lib64 to /usr/local/lib64

* Wed Aug 03 2011 Phillip Quiza <pquiza@nbcs.rutgers.edu> - 2.0-4.ru
- bugfixes and new features added to php code
- added 'chmod 555 /usr/lib64/webtools/webbin/restore'

* Thu May 19 2011 Jarek Sedlacek <jarek@nbcs.rutgers.edu> - 2.0-3.ru
- updated to new sources, added changelog and todo

* Tue May 10 2011 Jarek Sedlacek <jarek@nbcs.rutgers.edu> - 2.0-1.ru
- Break-off package from webtools
